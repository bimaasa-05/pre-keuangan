<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_pages_render(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create([
            'name' => 'Iuran Kas Siswa',
            'type' => 'income',
            'icon' => 'users',
        ]);
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 150000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Iuran Kas Kelas X-1',
        ]);

        $this->actingAs($user);

        $this->get('/dashboard')->assertOk()->assertSee('Dashboard Kas');
        $this->get('/transactions')->assertOk()->assertSee('Riwayat Transaksi');
        $this->get('/transactions/create')->assertOk()->assertSee('Catat Transaksi');
        $this->get('/transactions/'.$transaction->id)->assertOk()->assertSee('Bukti Nota');
        $this->get('/transactions/'.$transaction->id.'/edit')->assertOk()->assertSee('Edit Transaksi');
        $this->get('/categories')->assertOk()->assertSee('Kategori Kas');
        $this->get('/reports')->assertOk()->assertSee('Laporan Kas');
        $this->get('/users')->assertOk()->assertSee('Manajemen Pengguna');
    }

    public function test_category_can_be_created_updated_and_deleted(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->post('/categories', [
            'name' => 'Kategori Uji',
            'type' => 'expense',
            'icon' => 'tag',
        ])->assertRedirect();

        $category = Category::query()->where('name', 'Kategori Uji')->firstOrFail();

        $this->actingAs($user)->put('/categories/'.$category->id, [
            'name' => 'Kategori Uji Baru',
            'type' => 'expense',
            'icon' => 'sparkles',
        ])->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Kategori Uji Baru',
            'icon' => 'sparkles',
        ]);

        $this->actingAs($user)
            ->delete('/categories/'.$category->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_receipt_can_be_streamed_to_authenticated_user(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Sumbangan',
            'type' => 'income',
            'icon' => 'gift',
        ]);

        $this->actingAs($user)->post('/transactions', [
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 500000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Sumbangan komite',
            'receipt' => UploadedFile::fake()->image('nota.jpg'),
        ]);

        $transaction = Transaction::query()->firstOrFail();

        $this->actingAs($user)
            ->get('/transactions/'.$transaction->id.'/receipt')
            ->assertOk();
    }
}
