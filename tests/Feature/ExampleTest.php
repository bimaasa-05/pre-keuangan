<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_renders_public_landing_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('KasK1ta')
            ->assertSee('Catat kas sekolah tanpa ribet.');
    }

    public function test_authenticated_user_sees_dashboard_link_on_landing_page(): void
    {
        $user = User::factory()->create(['role' => 'bendahara']);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertDontSee('daftarOpen = true');
    }
}
