<?php 

namespace Pterodactyl\Http\Requests\Admin\Domain;

use Pterodactyl\Http\Requests\Admin\AdminFormRequest;

class DomainFormRequest extends AdminFormRequest {
    
    public function rules(): array 
    {
        $rules = [
            "server_id" => "nullable|exists:servers,id"
        ];

        if($this->method() === "POST") {
            $rules["domain"] = "required|string|min:3";
        }

        return $rules;
    }
}


