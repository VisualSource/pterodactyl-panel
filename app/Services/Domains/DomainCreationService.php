<?php

namespace Pterodactyl\Services\Domains;

use Pterodactyl\Models\Domain;
use Illuminate\Support\Facades\Artisan;
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

        if (!env('TH_NETWORK_DRYRUN', false)) {
            Artisan::queue('network:domain', [
                'name' => $domain->domain,
                '--unregister' => true,
            ]);
        }

        return $domain;
    }
}
