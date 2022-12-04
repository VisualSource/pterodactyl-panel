<?php

namespace Pterodactyl\Http\Controllers\Api\Application\Users;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Pterodactyl\Models\User;
use Pterodactyl\Facades\Activity;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Http\Requests\Api\Application\Users\FetchUserRequest;

class ExternalLoginController extends Controller 
{
 
    /**
     * Determine if the user is logging in using an email or username.
     */
    protected function getField(string $input = null): string
    {
        return ($input && str_contains($input, '@')) ? 'email' : 'username';
    }

    /**
    * Fire a failed login event.
    */
    protected function fireFailedLoginEvent(Authenticatable $user = null, array $credentials = [])
    {
        Event::dispatch(new Failed('auth', $user, $credentials));
    }

    /**
    * Get the failed login response instance.
    */
    protected function sendFailedLoginResponse(Request $request, Authenticatable $user = null, string $message = null)
    {
        $this->fireFailedLoginEvent($user, [
            $this->getField($request->input('username')) => $request->input('username'),
        ]);

        return new JsonResponse([
            'message' => trans('auth.failed')
        ],401);
    }
    public function index(FetchUserRequest $request): JsonResponse {

        try {
            $username = $request->input('username');

             /** @var \Pterodactyl\Models\User $user*/ 
            $user = User::query()->where($this->getField($username), $username)->firstOrFail();
        } catch (ModelNotFoundException) {
            return $this->sendFailedLoginResponse($request);
        }

        // Ensure that the account is using a valid username and password before trying to
        // continue. Previously this was handled in the 2FA checkpoint, however that has
        // a flaw in which you can discover if an account exists simply by seeing if you
        // can proceed to the next step in the login process.
        if (!password_verify($request->input('password'), $user->password)) {
            return $this->sendFailedLoginResponse($request, $user);
        }

        Activity::event('auth:checkpoint')->withRequestMetadata()->subject($user)->log();

        $data = $user->toVueObject();

        $data["totp_secret"] = $user->totp_secret;

        return new JsonResponse([
            "object" => "user",
            "attributes" => $data
        ]);
    }
}