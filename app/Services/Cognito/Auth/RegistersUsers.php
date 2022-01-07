<?php
declare(strict_types=1);

namespace App\Services\Cognito\Auth;

use Illuminate\Http\Response;
use App\Services\Cognito\CognitoClient;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Services\Cognito\Exceptions\InvalidUserFieldException;
// use Illuminate\Auth\Foundation\RegistersUsers as BaseSendsRegistersUsers;

trait RegistersUsers
{
    // use BaseSendsRegistersUsers;

    /**
     * Handle a registration request for the application.
     *
     * @param  RegistrationRequest  $request
     * @return Response
     *
     * @throws InvalidUserFieldException
     */
    public function register(Request $request): Response
    {
        $attributes = [];

        $userFields = config('cognito.sso_user_fields');

        foreach ($userFields as $userField) {
            if ($request->filled($userField)) {
                $attributes[$userField] = $request->get($userField);
            } else {
                throw new InvalidUserFieldException("The configured user field {$userField} is not provided in the request.");
            }
        }

        app()->make(CognitoClient::class)->register($request->username, $request->password, $attributes);

        event(new Registered($user = $this->create($request->all())));

        $this->registered($request, $user);

        return response('', Response::HTTP_CREATED);
    }
}
