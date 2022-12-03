<?php 

namespace Pterodactyl\Services\Domains;

use Pterodactyl\Models\Domain;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;


class DomainUpdateService 
{

     /**
     * DomainUpdateService constructor.
     */
    public function __construct(protected DomainRepositoryInterface $repository)
    {
        
    }

    /**
     * Update an existing domain.
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function handle(Domain|int $id, array $data): Domain {

        $id = ($id instanceof Domain) ? $id->id : $id;

        return $this->repository->update($id,$data);
    }

}