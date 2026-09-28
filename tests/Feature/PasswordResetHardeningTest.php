<?php

namespace Tests\Feature;

use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder', '--force' => true]);
    }

    public function test_forgot_password_request_is_throttled(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('password.email'), ['email' => 'tidak-ada@example.com']);
        }

        $this->post(route('password.email'), ['email' => 'tidak-ada@example.com'])
            ->assertStatus(429);
    }

    public function test_self_service_reset_is_logged_and_notified(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'pengurus@itg.ac.id']);
        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('password_reset_logs', [
            'user_id' => $user->id,
            'actor_id' => $user->id,
            'actor_role' => 'self',
        ]);

        Mail::assertSent(PasswordChangedMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_admin_manual_reset_is_logged_and_notified(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['username' => 'adminbkhm']);
        $admin->assignRole('bkhm');

        $target = User::factory()->create(['username' => 'targetormawa']);
        $target->assignRole('ormawa');

        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'username' => $target->username,
            'role' => 'ormawa',
            'status_akun' => 'aktif',
            'password' => 'password-baru-123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('password_reset_logs', [
            'user_id' => $target->id,
            'actor_id' => $admin->id,
            'actor_role' => 'bkhm',
        ]);

        Mail::assertSent(PasswordChangedMail::class, fn ($mail) => $mail->hasTo($target->email));
    }
}
