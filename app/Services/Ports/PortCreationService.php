<?php 

namespace Pterodactyl\Services\Ports;

use Illuminate\Support\Facades\Artisan;
use Pterodactyl\Models\Port;
use Pterodactyl\Contracts\Repository\PortRepositoryInterface;

class PortCreationService 
{
    public function __construct(protected PortRepositoryInterface $repository) 
    {
        
    }

    /**
    * Create a new domain.
    *
    * @throws \Pterodactyl\Exceptions\Model\DataValidationException
    */
    public function handle(array $data): Port 
    {

        /**@var Port $port */
        $port = $this->repository->create($data);

        $args = [
            'action' => "open",
            'port' => $port->external_port,
            "-d" => $port->description,
            '-t' => $port->type,
            '-m' => $port->method
        ];

        if(!is_null($port->internal_port)) {
            $args["internal"] = $port->internal_port;
        }

        if(!is_null($port->internal_address)) {
            $args["--i"] = $port->internal_address;
        }

        Artisan::queue("network:port",$args);

        return $port;
    }
}