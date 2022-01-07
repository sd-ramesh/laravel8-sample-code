<?php
declare(strict_types=1);

namespace App\Services\Cognito\Auth;

use Illuminate\Http\Request;
use App\Services\Cognito\CognitoClient;
use Illuminate\Support\Facades\Password;
use Collective\Auth\Foundation\ResetsPasswords as BaseResetsPasswords;

trait ResetsPasswords
{
    use BaseResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $client = app()->make(CognitoClient::class);

        $user = $client->getUser($request->email);

        if ($user['UserStatus'] === CognitoClient::FORCE_PASSWORD_STATUS) {
            $response = $this->forceNewPassword($request);
        } else {
            $response = $client->resetPassword($request->token, $request->email, $request->password);
        }

        return $response === Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * If a user is being forced to set a new password for the first time follow that flow instead.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    private function forceNewPassword($request)
    {
        $client = app()->make(CognitoClient::class);
        $login = $client->authenticate($request->email, $request->token);

        return $client->confirmPassword($request->email, $request->password, $login->get('Session'));
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return void
     */
    public function showResetForm(Request $request, $token = null)
    {
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }
}
