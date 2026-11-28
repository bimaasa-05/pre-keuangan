<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\HelpArticle;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RevisionFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_landing_and_help_pages_render(): void
    {
        HelpArticle::create([
            'title' => 'Panduan Transaksi',
            'slug' => 'panduan-transaksi',
            'category' => 'Transaksi',
            'summary' => 'Ringkasan panduan.',
            'content' => 'Isi panduan transaksi.',
            'is_published' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('KasK1ta')
            ->assertSee('Catat kas sekolah tanpa ribet.');

        $this->get('/help')
            ->assertOk()
            ->assertSee('Pusat Bantuan')
            ->assertSee('Panduan Transaksi');

        $this->get('/help/panduan-transaksi')
            ->assertOk()
            ->assertSee('Isi panduan transaksi.');
    }

    public function test_draft_help_article_is_hidden_from_public(): void
    {
        $article = HelpArticle::create([
            'title' => 'Artikel Draft',
            'slug' => 'artikel-draft',
            'category' => 'Umum',
            'content' => 'Belum dipublikasikan.',
            'is_published' => false,
        ]);

        $this->get('/help')->assertOk()->assertDontSee('Artikel Draft');
        $this->get('/help/'.$article->slug)->assertNotFound();
    }

    public function test_only_super_admin_can_open_landing_and_help_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($admin)->get('/super-admin/landing')->assertForbidden();
        $this->actingAs($admin)->get('/super-admin/help')->assertForbidden();

        $this->actingAs($superAdmin)->get('/super-admin/landing')->assertOk()->assertSee('Landing Page');
        $this->actingAs($superAdmin)->get('/super-admin/help')->assertOk()->assertSee('Help Center');
    }

    public function test_super_admin_can_update_landing_content(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->put('/super-admin/landing', [
            'hero' => [
                'eyebrow' => 'Baru',
                'title' => 'Judul Landing Baru',
                'subtitle' => 'Subjudul landing baru.',
                'cta_text' => 'Masuk',
            ],
            'features' => [
                ['icon' => 'wallet', 'title' => 'Fitur Baru', 'description' => 'Deskripsi fitur baru.'],
            ],
            'about' => [
                'title' => 'Tentang Baru',
                'text' => 'Teks tentang baru.',
                'points_text' => "Poin satu\nPoin dua",
            ],
            'announcement' => 'Pengumuman baru.',
            'contact' => [
                'email' => 'baru@sekolah.sch.id',
                'phone' => '08123',
                'address' => 'Alamat baru',
            ],
            'footer' => 'Footer baru',
        ])->assertRedirect(route('super-admin.landing.edit'));

        $this->assertDatabaseHas('site_settings', ['key' => 'landing']);
        $this->assertDatabaseCount('notifications', 1);

        $this->get('/')
            ->assertOk()
            ->assertSee('Judul Landing Baru')
            ->assertSee('Pengumuman baru.')
            ->assertSee('Fitur Baru')
            ->assertDontSee('Bukti nota tersimpan');
    }

    public function test_super_admin_can_manage_help_articles(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->post('/super-admin/help', [
            'title' => 'Cara Cetak Laporan',
            'category' => 'Laporan',
            'summary' => 'Ringkasan cetak.',
            'content' => 'Langkah mencetak laporan.',
            'is_published' => '1',
            'sort_order' => 2,
        ])->assertRedirect(route('super-admin.help.index'));

        $article = HelpArticle::query()->where('title', 'Cara Cetak Laporan')->firstOrFail();
        $this->assertSame('cara-cetak-laporan', $article->slug);
        $this->assertTrue($article->is_published);

        $this->actingAs($superAdmin)->put('/super-admin/help/'.$article->slug, [
            'title' => 'Cara Cetak Laporan Baru',
            'category' => 'Laporan',
            'summary' => 'Ringkasan baru.',
            'content' => 'Langkah baru.',
            'is_published' => '1',
            'sort_order' => 3,
        ])->assertRedirect(route('super-admin.help.index'));

        $this->assertDatabaseHas('help_articles', [
            'id' => $article->id,
            'title' => 'Cara Cetak Laporan Baru',
            'sort_order' => 3,
        ]);
    }

    public function test_transaction_actions_create_notifications_and_can_be_read(): void
    {
        $user = User::factory()->create(['role' => 'bendahara']);
        $category = Category::create([
            'name' => 'Iuran Kas',
            'type' => 'income',
            'icon' => 'users',
        ]);

        $this->actingAs($user)->post('/transactions', [
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 150000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Iuran Kas Kelas X-1',
        ])->assertRedirect();

        $this->assertDatabaseCount('notifications', 1);

        $notification = DB::table('notifications')->first();
        $this->assertSame('Transaksi dicatat', json_decode($notification->data, true)['title']);

        $this->actingAs($user)->get('/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.title', 'Transaksi dicatat');

        $this->actingAs($user)->post('/notifications/'.$notification->id.'/read')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull(DB::table('notifications')->first()->read_at);
    }

    public function test_realtime_transaction_and_report_partials_render(): void
    {
        $user = User::factory()->create(['role' => 'bendahara']);
        $category = Category::create([
            'name' => 'Iuran Kas',
            'type' => 'income',
            'icon' => 'users',
        ]);
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 250000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Iuran Realtime',
        ]);

        $this->actingAs($user)->get(route('transactions.results', ['search' => 'Realtime']))
            ->assertOk()
            ->assertSee('data-realtime-summary', false)
            ->assertSee('data-realtime-results', false)
            ->assertSee('Iuran Realtime');

        $this->actingAs($user)->get(route('reports.results', [
            'mode' => 'month',
            'month' => now()->month,
            'year' => now()->year,
        ]))
            ->assertOk()
            ->assertSee('data-report-header', false)
            ->assertSee('data-report-summary', false)
            ->assertSee('data-report-table', false)
            ->assertSee('Iuran Realtime');
    }

    public function test_realtime_transaction_pagination_uses_index_path_and_keeps_filters(): void
    {
        $user = User::factory()->create(['role' => 'bendahara']);
        $category = Category::create([
            'name' => 'Iuran Pagination',
            'type' => 'income',
            'icon' => 'users',
        ]);

        foreach (range(1, 11) as $index) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => 'income',
                'amount' => 10000 + $index,
                'transaction_date' => now()->toDateString(),
                'description' => 'Iuran Pagination '.$index,
            ]);
        }

        $this->actingAs($user)->get(route('transactions.results', [
            'search' => 'Iuran Pagination',
        ]))
            ->assertOk()
            ->assertSee('page=2', false)
            ->assertSee('search=Iuran', false)
            ->assertDontSee('/transactions/results?page=2', false);
    }
}
