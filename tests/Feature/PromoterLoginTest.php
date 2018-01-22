<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PromoterLoginTest extends TestCase
{
   use DatabaseMigrations;

    /** @test */
    public function logging_with_valid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@doe.com',
            'password' => bcrypt('some-long-password'),
        ]);
        $response = $this->post('/login', [
            'email' => 'jane@doe.com',
            'password' => 'some-long-password',
        ]);

        $response->assertRedirect('/backstage/concerts');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function logging_with_invalid_credentials()
    {
        factory(User::class)->create([
            'email' => 'jane@doe.com',
            'password' => bcrypt('some-long-password'),
        ]);
        $response = $this->post('/login', [
            'email' => 'jane@doe.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_with_none_existing_account()
    {
        factory(User::class)->create([
            'email' => 'jane@doe.com',
            'password' => bcrypt('some-long-password'),
        ]);
        $response = $this->post('/login', [
            'email' => 'john@doe.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }
}
