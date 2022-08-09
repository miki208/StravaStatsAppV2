<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

use App\Models\User;

class StravaController extends Controller
{
    private array $error_codes = [
        'authorization_rejected' => 'Please check if you have accepted the authorization with the Strava service.',
        'authorization_grant_invalid' => 'An external error occurred. Try again later, and if the problem persists, don\'t hesitate to contact us.',
        'invalid_scope' => 'Please check if you have given all the required permissions to the app during the authorization.',
        'external_service_unreachable' => 'The external service is not reachable at the moment. Please try again later.',
        'token_exchange_failed' => 'Authorization problem. Please try again later, and if the problem persists, don\'t hesitate to contact us.',
        'internal_error' => 'An internal error occurred. Please try again later, and if the problem persists, don\'t hesitate to contact us.',
        'already_registered' => 'You\'re already registered. If something does not work as expected, don\'t hesitate to contact us.'
    ];

    public function authorization_grant_callback(Request $request)
    {
        if($request->has('error'))
        {
            $reason = $request->input('error');

            Log::error("Unsuccessful attempt to authorize user. Reason: $reason.");

            return redirect('registration_failed')->with('error_code', 'authorization_rejected');
        }

        if(!$request->has(['code', 'scope']))
        {
            Log::error("Unsuccessful attempt to authorize user. Reason: Mandatory parameters in the authorization grant callback are missing.");

            return redirect('registration_failed')->with('error_code', 'authorization_grant_invalid');
        }

        $code = $request->input('code');
        $scope = $request->input('scope');

        $scope_tokens = explode(',', $scope);

        if(!in_array('activity:read_all', $scope_tokens))
        {
            Log::error("Unsuccessful attempt to authorize user. Reason: Invalid scope.");

            return redirect('registration_failed')->with('error_code', 'invalid_scope');
        }

        try {
            $response = Http::post('https://www.strava.com/oauth/token', [
                'client_id' => config('strava-service.client-id'),
                'client_secret' => config('strava-service.client-secret'),
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]);
        }
        catch(ConnectionException)
        {
            Log::error("Unsuccessful attempt to authorize user. Reason: Unable to exchange authorization grant for access token since external service can't be contacted.");

            return redirect('registration_failed')->with('error_code', 'external_service_unreachable');
        }

        if(!$response->successful())
        {
            $status = $response->status();

            Log::error("Unsuccessful attempt to authorize user. Reason: Unable to exchange authorization grant for access token (got status $status).");

            return redirect('registration_failed')->with('error_code', 'token_exchange_failed');
        }

        $response_data = $response->json();

        $access_token = $response_data['access_token'] ?? null;
        $refresh_token = $response_data['refresh_token'] ?? null;
        $expires_at = $response_data['expires_at'] ?? null;
        $athlete_info = $response_data['athlete'] ?? null;

        if($athlete_info == null)
        {
            Log::error("Unsuccessful attempt to authorize user. Reason: Can't find athlete info.");

            return redirect('registration_failed')->with('error_code', 'internal_error');
        }

        $user = User::where('user_id', $athlete_info['id'])->first();

        if($user == null)
        {
            //--- ok, create new user

            $user = new User;

            $user->user_id = $athlete_info['id'];
            $user->access_token = $access_token;
            $user->refresh_token = $refresh_token;
            $user->expires_at = $expires_at;

            $user->name = $athlete_info['firstname'] ?? '';

            $user->save();

            return redirect('registration_successful')->with('name', $user->name);
        }
        else
        {
            return redirect('registration_failed')->with('error_code', 'already_registered');
        }
    }

    public function registration_failed(Request $request)
    {
        if(!$request->session()->has('error_code'))
        {
            return redirect('register');
        }
        else
        {
            $err_code = $request->session()->get('error_code');

            if(array_key_exists($err_code, $this->error_codes))
            {
                $err_msg = $this->error_codes[$err_code];
            }
            else
            {
                $err_msg = 'Unknown error.';
            }
        }

        return view('registration_failed', [
            'error_message' => $err_msg
        ]);
    }

    public function registration_successful(Request $request)
    {
        if(!$request->session()->has('name'))
        {
            return redirect('register');
        }
        else
        {
            $name = $request->session()->get('name');

            return view('registration_successful', [
               'name' => $name
            ]);
        }
    }
}
