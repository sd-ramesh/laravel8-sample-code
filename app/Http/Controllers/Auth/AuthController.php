<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use function \response, \auth;
use App\Models\User;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ( $validator->fails() )
        {
            return response()
                ->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST)
                ->header('X-Status-Reason', 'Validation failed');
        }

        if ($token = $this->jwt()->attempt($validator->validated()))
        {
            return $this->respondWithToken($token);
        }

        return response()
            ->json(['errors' => ['Unauthorized']], Response::HTTP_UNAUTHORIZED)
            ->header('X-Status-Reason', 'Authentication failed');
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        return response()->json($this->authUser());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->jwt()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails())
        {
            return response()
                ->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST)
                ->header('X-Status-Reason', 'Validation failed');
        }

        User::create(array_merge(
            $validator->validated(),
            ['password' => Hash::make($request->input('password'))]
        ));

        return response()->json(['message' => 'User successfully registered'], Response::HTTP_CREATED);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken($this->jwt()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->jwt()->factory()->getTTL() * 60,
            'user' => $this->authUser()
        ]);
    }

    /**
     * @return array
     */
    protected function authUser(): array
    {
        return $this->jwt()->user()->toArray();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|\Laravel\Lumen\Application|\Tymon\JWTAuth\JWT
     */
    protected function jwt()
    {
        return auth();
    }
}
