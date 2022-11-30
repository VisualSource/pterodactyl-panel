<?php

namespace Pterodactyl\Services\Domains;

use Pterodactyl\Models\Domain;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;

class DomainDeletionService 
{
    public function __construct(private DomainRepositoryInterface $repository)
    {
        
    }

    public function handle(int $domain){

    }
}
?>