<?php

namespace Tests\Feature\User\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_success(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'user');

        $response = $this->withHeaders(['referer' => 'http://localhost:3000'])
            ->post(route('logout'));

        $response->assertStatus(200)->assertJson([
            'message' => 'ログアウトしました。'
        ]);
    }
}