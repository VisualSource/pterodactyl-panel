<?php 

namespace Pterodactyl\Http\Requests\Admin;

use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Pterodactyl\Models\Port;

class PortFormRequest extends AdminFormRequest {
    
    public function rules(): array 
    {
        return Port::getRules();
    }
}