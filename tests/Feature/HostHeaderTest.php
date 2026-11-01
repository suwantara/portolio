<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('password reset links use the configured application URL', function () {
    Notification::fake();

    $user = User::factory()->create();
    $base = rtrim((string) config('app.url'), '/');

    $this->withHeader('Host', 'evil.example')
        ->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user, $base): bool {
            $url = $notification->toMail($user)->actionUrl;

            expect($url)->toStartWith($base);

            return true;
        },
    );
});
