<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_bendahara_cannot_access_user_management(): void
    {
        $bendahara = User::factory()->create(['role' => 'bendahara']);

        $this->actingAs($bendahara)->get('/users')->assertForbidden();
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/users')->assertOk();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Bendahara Baru',
            'email' => 'bendahara.baru@example.com',
            'role' => 'bendahara',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Bendahara Baru',
            'email' => 'bendahara.baru@example.com',
            'role' => 'bendahara',
        ]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete('/users/'.$admin->id)
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
