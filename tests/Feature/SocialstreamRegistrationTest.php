<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Session;
use JoelButcher\Socialstream\Providers;
use Laravel\Fortify\Features as FortifyFeatures;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * @return array<int, array<int, string>>
 */
dataset('socialiteProvidersDataProvider', function () {
    return [
        [Providers::bitbucket()],
        [Providers::facebook()],
        [Providers::github()],
        [Providers::gitlab()],
        [Providers::google()],
        [Providers::linkedin()],
        [Providers::linkedinOpenId()],
        [Providers::slack()],
        [Providers::twitterOAuth1()],
        [Providers::twitterOAuth2()],
    ];
});

test('users get redirected correctly', function (string $provider) {
    if (! Providers::enabled($provider)) {
        $this->markTestSkipped("Registration support with the $provider provider is not enabled.");
    }

    config()->set("services.$provider", [
        'client_id' => 'client-id',
        'client_secret' => 'client-secret',
        'redirect' => "http://localhost/oauth/$provider/callback",
    ]);

    $response = $this->get("/oauth/$provider");
    $response->assertRedirectContains($provider);
})->with('socialiteProvidersDataProvider');

test('users can register using socialite providers', function (string $socialiteProvider) {
    if (! FortifyFeatures::enabled(FortifyFeatures::registration())) {
        $this->markTestSkipped('Registration support is not enabled.');
    }

    if (! Providers::enabled($socialiteProvider)) {
        $this->markTestSkipped("Registration support with the $socialiteProvider provider is not enabled.");
    }

    $user = (new User())
        ->map([
            'id' => 'abcdefgh',
            'nickname' => 'Jane',
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'avatar' => null,
            'avatar_original' => null,
        ])
        ->setToken('user-token')
        ->setRefreshToken('refresh-token')
        ->setExpiresIn(3600);

    $provider = Mockery::mock('Laravel\\Socialite\\Two\\'.$socialiteProvider.'Provider');
    $provider->shouldReceive('user')->once()->andReturn($user);

    Socialite::shouldReceive('driver')->once()->with($socialiteProvider)->andReturn($provider);

    Session::put('socialstream.previous_url', route('register'));

    $response = $this->get("/oauth/$socialiteProvider/callback");

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
})->with('socialiteProvidersDataProvider');
