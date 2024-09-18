<?php

namespace Tests\Feature\User\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_success(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders(['referer' => 'http://localhost:3000'])
          ->post(route('login'), [
              'email' => $user->email,
              'password' => 'password',
          ]);

        $response->assertStatus(200);
    }
}