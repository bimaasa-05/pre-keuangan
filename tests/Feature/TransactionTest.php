<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_transactions(): void
    {
        $this->get('/transactions')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Iuran Kas Siswa',
            'type' => 'income',
            'icon' => 'users',
        ]);

        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 150000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Iuran Kas Kelas X-1',
            'notes' => 'Iuran bulanan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 150000,
            'description' => 'Iuran Kas Kelas X-1',
        ]);
    }

    public function test_category_type_must_match_transaction_type(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Iuran Kas Siswa',
            'type' => 'income',
            'icon' => 'users',
        ]);

        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 150000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Salah tipe',
        ]);

        $response->assertSessionHasErrors('category_id');
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_user_can_update_and_delete_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Pembelian ATK',
            'type' => 'expense',
            'icon' => 'pencil-square',
        ]);
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 250000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Beli ATK',
        ]);

        $this->actingAs($user)->put('/transactions/'.$transaction->id, [
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 275000,
            'transaction_date' => now()->toDateString(),
            'description' => 'Beli ATK dan fotokopi',
        ])->assertRedirect(route('transactions.show', $transaction));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 275000,
            'description' => 'Beli ATK dan fotokopi',
        ]);

        $this->actingAs($user)
            ->delete('/transactions/'.$transaction->id)
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    public function test_receipt_upload_is_stored(): void
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
        ])->assertRedirect();

        $transaction = Transaction::query()->firstOrFail();

        $this->assertNotNull($transaction->receipt_path);
        Storage::disk('public')->assertExists($transaction->receipt_path);
    }
}
