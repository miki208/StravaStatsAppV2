<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
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

    public function register_user()
    {
        return view('register', [
            'client_id' => config('strava-service.client-id'),
            'redirect_uri' => config('app.url') . config('strava-service.authorization-callback-url')
        ]);
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
