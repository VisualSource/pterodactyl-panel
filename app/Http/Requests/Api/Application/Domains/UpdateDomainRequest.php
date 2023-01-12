<?php 

namespace Pterodactyl\Http\Requests\Api\Application\Domains;

use Pterodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class UpdateDomainRequest extends ApplicationApiRequest {
    
    public function rules(): array 
    {
        $rules = [
            "server_id" => "nullable|exists:servers,id"
        ];

        return $rules;
    }
}


