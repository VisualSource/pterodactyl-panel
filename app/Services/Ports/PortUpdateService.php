<?php
namespace Pterodactyl\Services\Ports;

use Pterodactyl\Models\Port;
use Pterodactyl\Contracts\Repository\PortRepositoryInterface;

class PortUpdateService 
{
    public function __construct(
        protected PortRepositoryInterface $repository
    ) {
        
    }

    /**
    * Update an existing port.
    *
    * @throws \Pterodactyl\Exceptions\Model\DataValidationException
    * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
    */
    public function handle(Port|int $id, array $data): Port {
        
        $id = ($id instanceof Port) ? $id->id : $id;

        $port = $this->repository->update($id,$data);

        return $port;
    }
}
