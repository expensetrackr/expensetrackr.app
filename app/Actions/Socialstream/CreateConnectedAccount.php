<?php

declare(strict_types=1);

namespace App\Actions\Socialstream;

use App\Models\User;
use JoelButcher\Socialstream\ConnectedAccount;
use JoelButcher\Socialstream\Contracts\CreatesConnectedAccounts;
use JoelButcher\Socialstream\Socialstream;
use Laravel\Socialite\Contracts\User as ProviderUser;

final class CreateConnectedAccount implements CreatesConnectedAccounts
{
    /**
     * Create a connected account for a given user.
     *
     * @param  User  $user
     */
    public function create(mixed $user, string $provider, ProviderUser $providerUser): ConnectedAccount
    {
        /** @var ConnectedAccount */
        return Socialstream::connectedAccountModel()::forceCreate([
            'user_id' => $user->id,
            'provider' => mb_strtolower($provider),
            'provider_id' => $providerUser->getId(),
            'name' => $providerUser->getName(),
            'nickname' => $providerUser->getNickname(),
            'email' => $providerUser->getEmail(),
            'avatar_path' => $providerUser->getAvatar(),
            'token' => $providerUser->token ?? null,
            'secret' => $providerUser->tokenSecret ?? null,
            'refresh_token' => $providerUser->refreshToken ?? null,
            'expires_at' => property_exists($providerUser, 'expiresIn') ? now()->addSeconds(type($providerUser->expiresIn)->asInt()) : null,
        ]);
    }
}
