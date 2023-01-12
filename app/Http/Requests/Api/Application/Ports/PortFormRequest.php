<?php 

namespace Pterodactyl\Http\Requests\Api\Application\Ports;

use Pterodactyl\Http\Requests\Api\Application\ApplicationApiRequest;
use Pterodactyl\Models\Port;

class PortFormRequest extends ApplicationApiRequest {
    
    public function rules(): array 
    {
        return Port::getRules();
    }
}