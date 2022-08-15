<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Pterodactyl\Facades\Activity;
use Pterodactyl\Services\Users\UserUpdateService;
use Pterodactyl\Transformers\Api\Client\AccountTransformer;
use Pterodactyl\Http\Requests\Api\Client\Account\UpdateEmailRequest;
use Pterodactyl\Http\Requests\Api\Client\Account\UpdatePasswordRequest;

class AccountController extends ClientApiController
{
    /**
     * @var \Pterodactyl\Services\Users\UserUpdateService
     */
    private $updateService;

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $manager;

    /**
     * AccountController constructor.
     */
    public function __construct(AuthManager $manager, UserUpdateService $updateService)
    {
        parent::__construct();

        $this->updateService = $updateService;
        $this->manager = $manager;
    }

    public function index(Request $request): array
    {
        return $this->fractal->item($request->user())
            ->transformWith($this->getTransformer(AccountTransformer::class))
            ->toArray();
    }

    /**
     * Update the authenticated user's email address.
     */
    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $original = $request->user()->email;
        $this->updateService->handle($request->user(), $request->validated());

        if ($original !== $request->input('email')) {
            Activity::event('user:account.email-changed')
                ->property(['old' => $original, 'new' => $request->input('email')])
                ->log();
                
            try {
                // Curently our system has two DB's
                // so to keep emails synced between 
                // the local system DB and the customer DB
                // we have this webhook
                
                $user = $request->user();
                $data = array(
                    "email" => $request->input('email'),
                    "uuid" => $user->uuid
                ); 
                 
                $post_data = json_encode($data);
        
                $ctl = curl_init(env("APP_WEBHOOK_API"));
                curl_setopt($ctl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ctl, CURLINFO_HEADER_OUT, true);
                curl_setopt($ctl, CURLOPT_POST,       true);
                curl_setopt($ctl, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ctl, CURLOPT_HTTPHEADER,array(
                    "Content-Type: application/json",
                    "Content-Length: " . strlen($post_data),
                    "Authorization: Bearer " . env("APP_WEBHOOK_TOKEN")
                ));
                curl_exec($ctl);
                curl_close($ctl);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Update the authenticated user's password. All existing sessions will be logged
     * out immediately.
     *
     * @throws \Throwable
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $this->updateService->handle($request->user(), $request->validated());

        $guard = $this->manager->guard();
        // If you do not update the user in the session you'll end up working with a
        // cached copy of the user that does not include the updated password. Do this
        // to correctly store the new user details in the guard and allow the logout
        // other devices functionality to work.
        $guard->setUser($user);

        // This method doesn't exist in the stateless Sanctum world.
        if (method_exists($guard, 'logoutOtherDevices')) {
            $guard->logoutOtherDevices($request->input('password'));
        }

        Activity::event('user:account.password-changed')->log();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
