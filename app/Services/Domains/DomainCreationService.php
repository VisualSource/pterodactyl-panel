<?php 

namespace Pterodactyl\Services\Domains;

use Illuminate\Support\Facades\Artisan;
use Pterodactyl\Models\Domain;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;

class DomainCreationService 
{
    /**
    * DomainCreationService constructor.
    */
    public function __construct(protected DomainRepositoryInterface $repository)
    {
        
    }

    /**
    * Create a new domain.
    *
    * @throws \Pterodactyl\Exceptions\Model\DataValidationException
    */
    public function handle(array $data): Domain 
    {
        $domain = $this->repository->create($data);

        Artisan::queue("network:domain",[
            "name" => $domain->domain,
            "--unregister" => true
        ]);

        return $domain;
    }
}