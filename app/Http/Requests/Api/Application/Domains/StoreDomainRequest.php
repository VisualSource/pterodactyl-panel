<?php 

namespace Pterodactyl\Http\Requests\Api\Application\Domains;

use Pterodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class StoreDomainRequest extends ApplicationApiRequest {
    
    public function rules(): array 
    {
        $rules = [
            "server_id" => "nullable|exists:servers,id",
            "domain" => "required|string|min:3"
        ];

        return $rules;
    }
}
