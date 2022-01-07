<?php
declare(strict_types=1);

namespace App\Services\Cognito;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;
use App\Services\Cognito\Exceptions\UsernameExistsException;
use App\Services\Cognito\Exceptions\UserNotConfirmedException;

class CognitoClient
{
    public const NEW_PASSWORD_CHALLENGE = 'NEW_PASSWORD_REQUIRED';
    public const ADMIN_USER_PASSWORD_AUTH = 'ADMIN_USER_PASSWORD_AUTH';
    public const FORCE_PASSWORD_STATUS = 'FORCE_CHANGE_PASSWORD';
    public const RESET_REQUIRED = 'PasswordResetRequiredException';
    public const USER_NOT_FOUND = 'UserNotFoundException';
    public const USERNAME_EXISTS = 'UsernameExistsException';
    public const INVALID_PASSWORD = 'InvalidPasswordException';
    public const CODE_MISMATCH = 'CodeMismatchException';
    public const EXPIRED_CODE = 'ExpiredCodeException';

    /**
     * @var CognitoIdentityProviderClient
     */
    protected CognitoIdentityProviderClient $client;

    /**
     * @var string
     */
    protected string $clientId;

    /**
     * @var string
     */
    protected string $clientSecret;

    /**
     * @var string
     */
    protected string $poolId;

    /**
     * CognitoClient constructor.
     * @param CognitoIdentityProviderClient $client
     * @param string $clientId
     * @param string $clientSecret
     * @param string $poolId
     */
    public function __construct(
        CognitoIdentityProviderClient $client,
        $clientId,
        $clientSecret,
        $poolId
    )
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->poolId = $poolId;
    }

    /**
     * Checks if credentials of a user are valid.
     *
     * @see http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminInitiateAuth.html
     *
     * @param string $username
     * @param string $password
     *
     * @return \Aws\Result|bool
     * @throws CognitoIdentityProviderException
     */
    public function authenticate($username, $password)
    {
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow' => self::ADMIN_USER_PASSWORD_AUTH,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password,
                    'SECRET_HASH' => $this->cognitoSecretHash($username),
                ],
                'ClientId' => $this->clientId,
                'UserPoolId' => $this->poolId,
            ]);
        } catch (CognitoIdentityProviderException $exception) {
            if ($exception->getAwsErrorCode() === self::RESET_REQUIRED ||
                $exception->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return false;
            }

            throw $exception;
        }

        return $response;
    }

    /**
     * Registers a user in the given user pool.
     *
     * @param string $username  The username
     * @param string $password  The password
     * @param array $attributes
     *
     * @return bool
     */
    public function register($username, $password, array $attributes = []): bool
    {
        if ($this->isValidEmail($username)) {
            $attributes['email'] = $username;
        } else {
            $attributes['phone_number'] = $username;
        }

        try {
            $response = $this->client->signUp([
                'ClientId' => $this->clientId,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($username),
                'UserAttributes' => $this->formatAttributes($attributes),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USERNAME_EXISTS) {
               throw new UsernameExistsException($e->getAwsErrorMessage());
            }

            throw $e;
        }
        if(!(bool)$response['UserConfirmed']){
            throw new UserNotConfirmedException();
        }
        return true;
    }

    /**
     * Send a password reset code to a user.
     * @see http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_ForgotPassword.html
     *
     * @param string $username
     * @return string
     */
    public function sendResetLink($username)
    {
        try {
            $result = $this->client->forgotPassword([
                'ClientId' => $this->clientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return Password::INVALID_USER;
            }

            throw $e;
        }

        return Password::RESET_LINK_SENT;
    }

    /**
     * Reset a users password based on reset code.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_ConfirmForgotPassword.html.
     *
     * @param string $code
     * @param string $username
     * @param string $password
     * @return string
     */
    public function resetPassword($code, $username, $password)
    {
        try {
            $this->client->confirmForgotPassword([
                'ClientId' => $this->clientId,
                'ConfirmationCode' => $code,
                'Password' => $password,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return Password::INVALID_USER;
            }

            if ($e->getAwsErrorCode() === self::INVALID_PASSWORD) {
                return Lang::has('passwords.password') ? 'passwords.password' : $e->getAwsErrorMessage();
            }

            if ($e->getAwsErrorCode() === self::CODE_MISMATCH || $e->getAwsErrorCode() === self::EXPIRED_CODE) {
                return Password::INVALID_TOKEN;
            }

            throw $e;
        }

        return Password::PASSWORD_RESET;
    }

    /**
     * Register a user and send them an email to set their password.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminCreateUser.html.
     *
     * @param $username
     * @param array $attributes
     * @return bool
     */
    public function inviteUser($username, array $attributes = [])
    {
        $attributes['email'] = $username;
        $attributes['email_verified'] = 'true';

        try {
            $this->client->AdminCreateUser([
                'UserPoolId' => $this->poolId,
                'DesiredDeliveryMediums' => [
                    'EMAIL',
                ],
                'Username' => $username,
                'UserAttributes' => $this->formatAttributes($attributes),
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USERNAME_EXISTS) {
                return false;
            }

            throw $e;
        }

        return true;
    }

    /**
     * Set a new password for a user that has been flagged as needing a password change.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminRespondToAuthChallenge.html.
     *
     * @param string $username
     * @param string $password
     * @param string $session
     *
     * @return string
     * @throws CognitoIdentityProviderException
     */
    public function confirmPassword($username, $password, $session): string
    {
        try {
            $this->client->AdminRespondToAuthChallenge([
                'ClientId' => $this->clientId,
                'UserPoolId' => $this->poolId,
                'Session' => $session,
                'ChallengeResponses' => [
                    'NEW_PASSWORD' => $password,
                    'USERNAME' => $username,
                    'SECRET_HASH' => $this->cognitoSecretHash($username),
                ],
                'ChallengeName' => self::NEW_PASSWORD_CHALLENGE
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::CODE_MISMATCH || $e->getAwsErrorCode() === self::EXPIRED_CODE) {
                return Password::INVALID_TOKEN;
            }

            throw $e;
        }

        return Password::PASSWORD_RESET;
    }

    /**
     * @param string $username
     *
     * @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#admindeleteuser
     */
    public function deleteUser($username): void
    {
        if (config('cognito.delete_user')) {
            $this->client->adminDeleteUser([
                'UserPoolId' => $this->poolId,
                'Username' => $username,
            ]);
        }
    }

    /**
     * Sets the specified user's password in a user pool as an administrator.
     *
     * @see https://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminSetUserPassword.html
     *
     * @param string $username
     * @param string $password
     * @param bool $permanent
     *
     * @return string
     * @throws CognitoIdentityProviderException
     */
    public function setUserPassword($username, $password, $permanent = true): string
    {
        try {
            $this->client->adminSetUserPassword([
                'Password' => $password,
                'Permanent' => $permanent,
                'Username' => $username,
                'UserPoolId' => $this->poolId,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return Password::INVALID_USER;
            }

            if ($e->getAwsErrorCode() === self::INVALID_PASSWORD) {
                return Lang::has('passwords.password') ? 'passwords.password' : $e->getAwsErrorMessage();
            }

            throw $e;
        }

        return Password::PASSWORD_RESET;
    }

    /**
     * @param string $username
     * @return void
     */
    public function invalidatePassword($username): void
    {
        $this->client->adminResetUserPassword([
            'UserPoolId' => $this->poolId,
            'Username' => $username,
        ]);
    }

    /**
     * @param string $username
     * @return void
     */
    public function confirmSignUp($username): void
    {
        $this->client->adminConfirmSignUp([
            'UserPoolId' => $this->poolId,
            'Username' => $username,
        ]);
    }

    /**
     * @param string $username
     * @param string $confirmationCode
     *
     * @return string|void
     * @throws CognitoIdentityProviderException
     */
    public function confirmUserSignUp($username, $confirmationCode): string
    {
        try {
            $this->client->confirmSignUp([
                'ClientId' => $this->clientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username,
                'ConfirmationCode' => $confirmationCode,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            if ($e->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return 'validation.invalid_user';
            }

            if ($e->getAwsErrorCode() === self::CODE_MISMATCH || $e->getAwsErrorCode() === self::EXPIRED_CODE) {
                return 'validation.invalid_token';
            }

            if ($e->getAwsErrorCode() === 'NotAuthorizedException' AND $e->getAwsErrorMessage() === 'User cannot be confirmed. Current status is CONFIRMED') {
                return 'validation.confirmed';
            }

            if ($e->getAwsErrorCode() === 'LimitExceededException') {
                return 'validation.exceeded';
            }

            throw $e;
        }
        return ('User Confirmed');
    }

    /**
     * @param string $username
     *
     * @return string|void
     * @throws CognitoIdentityProviderException
     */
    public function resendToken($username): string
    {
        try {
            $this->client->resendConfirmationCode([
                'ClientId' => $this->clientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username
            ]);
        } catch (CognitoIdentityProviderException $e) {

            if ($e->getAwsErrorCode() === self::USER_NOT_FOUND) {
                return 'validation.invalid_user';
            }

            if ($e->getAwsErrorCode() === 'LimitExceededException') {
                return 'validation.exceeded';
            }

            if ($e->getAwsErrorCode() === 'InvalidParameterException') {
                return 'validation.confirmed';
            }

            throw $e;
        }
    }

    // HELPER FUNCTIONS

    /**
     * Set a users attributes.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_AdminUpdateUserAttributes.html.
     *
     * @param string $username
     * @param array $attributes
     * @return bool
     */
    public function setUserAttributes($username, array $attributes)
    {
        $this->client->AdminUpdateUserAttributes([
            'Username' => $username,
            'UserPoolId' => $this->poolId,
            'UserAttributes' => $this->formatAttributes($attributes),
        ]);

        return true;
    }

    /**
     * Creates the Cognito secret hash.
     * @param string $username
     *
     * @return string
     */
    protected function cognitoSecretHash($username): string
    {
        return $this->hash($username . $this->clientId);
    }

    /**
     * Creates a HMAC from a string.
     *
     * @param string $message
     * @return string
     */
    protected function hash($message): string
    {
        $hash = hash_hmac(
            'sha256',
            $message,
            $this->clientSecret,
            true
        );

        return base64_encode($hash);
    }

    /**
     * Get user details.
     * http://docs.aws.amazon.com/cognito-user-identity-pools/latest/APIReference/API_GetUser.html.
     *
     * @param string $username
     * @return mixed
     */
    public function getUser($username)
    {
        try {
            $user = $this->client->AdminGetUser([
                'Username' => $username,
                'UserPoolId' => $this->poolId,
            ]);
        } catch (CognitoIdentityProviderException $e) {
            return false;
        }

        return $user;
    }

    /**
     * Format attributes in Name/Value array.
     *
     * @param array $attributes
     * @return array
     */
    protected function formatAttributes(array $attributes): array
    {
        $userAttributes = [];

        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name' => $key,
                'Value' => (string)$value,
            ];
        }

        return $userAttributes;
    }

    /**
     * Validate email
     *
     * @param $email
     *
     * @return bool
     */
    protected function isValidEmail($email): bool
    {
        $rules = ['email' => 'email:filter,rfc,dns'];
        $input = ['email' => $email];

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = app('validator')->make($input, $rules);

        return $validator->passes();
    }

    /**
     * @param $accessToken
     * @return
     */
    public function signout($accessToken)
    {
        try
        {
             return $this
                ->client
                ->GlobalSignOut(['AccessToken' => $accessToken, ]);
        }
        catch(\Exception $e)
        {
            return ['error' => true, 'message' => $e->getAwsErrorMessage() ];
        }
    }
}
