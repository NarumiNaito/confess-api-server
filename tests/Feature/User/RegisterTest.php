<?php

namespace Tests\Feature\User\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_success(): void
    {
        $response = $this->post(route('register'), [
              'name' => 'test',
              'email' => 'test@example.com',
              'password' => 'password',
          ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'ユーザ登録が完了しました。'
        ]);
    }

    public function test_exists_name(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('register'), [
              'name' => $user->name,
              'email' => 'test@example.com',
              'password' => 'password',
          ]);

        $response->assertStatus(200)->assertJson([
            'message' => '名前がすでに登録されています。'
        ]);
    }

    public function test_exists_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('register'), [
              'name' => 'test',
              'email' => $user->email,
              'password' => 'password',
          ]);

        $response->assertStatus(200)->assertJson([
            'message' => 'メールアドレスがすでに登録されています。'
        ]);
    }
}