<?php

namespace Pterodactyl\Http\Requests\Api\Application\Ports;

use Pterodactyl\Models\Port;
use Pterodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class UpdatePortRequest extends ApplicationApiRequest
{
    public function rules(): array
    {
        return Port::getRules();
    }
}
