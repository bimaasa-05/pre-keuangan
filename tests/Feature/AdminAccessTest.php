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

    public function test_admin_cannot_access_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/users')->assertForbidden();
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->get('/users')->assertOk();
    }

    public function test_super_admin_can_create_user(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->post('/users', [
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

    public function test_super_admin_cannot_delete_own_account(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->delete('/users/'.$superAdmin->id)
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_super_admin_can_demote_another_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $other = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($other)->put('/users/'.$superAdmin->id, [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'role' => 'admin',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id, 'role' => 'admin']);
    }

    public function test_super_admin_cannot_change_own_role(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->put('/users/'.$superAdmin->id, [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'role' => 'admin',
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id, 'role' => 'super_admin']);
    }

    public function test_super_admin_can_demote_self_when_another_super_admin_exists(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)->put('/users/'.$superAdmin->id, [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'role' => 'admin',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id, 'role' => 'admin']);
    }
}
