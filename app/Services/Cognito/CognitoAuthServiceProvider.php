<?php
declare(strict_types=1);

namespace App\Services\Cognito;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

/**
 * @property-read Application $app
 */
class CognitoAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(CognitoClient::class, function (Application $app) {
            $config = [
                'region'      => config('cognito.region'),
                'version'     => config('cognito.version'),
            ];

            $credentials = config('cognito.credentials');

            if (! empty($credentials['key']) && ! empty($credentials['secret'])) {
                $config['credentials'] = Arr::only($credentials, ['key', 'secret', 'token']);
            }

            return new CognitoClient(
                new CognitoIdentityProviderClient($config),
                config('cognito.app_client_id'),
                config('cognito.app_client_secret'),
                config('cognito.user_pool_id')
            );
        });

        $this->app['auth']->extend('cognito', function (Application $app, $name, array $config) {

            $guard = new Auth\CognitoGuard(
                $name,
                $client = $app->make(CognitoClient::class),
                $app['auth']->createUserProvider($config['provider']),
                $app['session.store'],
                $app['request']
            );

//            $guard->setCookieJar($this->app['cookie']);
//            $guard->setDispatcher($this->app['events']);

            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));

            return $guard;
        });
    }
}
