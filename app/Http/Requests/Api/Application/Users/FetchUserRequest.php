<?php

namespace Pterodactyl\Http\Requests\Api\Application\Users;

use Pterodactyl\Services\Acl\Api\AdminAcl;
use Pterodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class FetchUserRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_USERS;
    protected int $permission = AdminAcl::READ;

    public function rules(array $rules = null): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
