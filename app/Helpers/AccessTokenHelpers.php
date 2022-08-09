<?php

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

if (! function_exists('RefreshAccessToken')) {
    function RefreshAccessToken(User $user): bool
    {
        try {
            $response = Http::post('https://www.strava.com/oauth/token', [
                'client_id' => config('strava-service.client-id'),
                'client_secret' => config('strava-service.client-secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $user->refresh_token
            ]);
        } catch (ConnectionException) {
            return false;
        }

        $response_json = $response->json();

        $access_token = $response_json['access_token'] ?? null;
        $expires_at = $response_json['expires_at'] ?? null;
        $refresh_token = $response_json['refresh_token'] ?? null;

        if (!$access_token or !$expires_at or !$refresh_token) {
            return false;
        }

        $user->access_token = $access_token;
        $user->expires_at = $expires_at;
        $user->refresh_token = $refresh_token;

        $user->save();

        return true;
    }
}

if (! function_exists('ShouldRefreshAccessToken')) {
    function ShouldRefreshAccessToken(User $user): bool
    {
        return $user->expires_at - time() < 30 * 60;
    }
}
