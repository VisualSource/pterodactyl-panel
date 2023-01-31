<?php

namespace Pterodactyl\Services\Domains;

use Webmozart\Assert\Assert;
use Pterodactyl\Models\Domain;
use Illuminate\Support\Facades\Artisan;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;

class DomainDeletionService
{
    /**
     * DomainDeletionService constructor.
     */
    public function __construct(protected DomainRepositoryInterface $repository)
    {
    }

    /**
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function handle(Domain|int $id): ?int
    {
        $id = ($id instanceof Domain) ? $id->id : $id;
        Assert::integerish($id, 'First argument passed to handle must be numeric or an instance of ' . Domain::class . ', received %s.');

        $domain = $this->repository->find($id);

        if (!env('TH_NETWORK_DRYRUN', false)) {
            Artisan::queue('network:domain', [
                'name' => $domain->domain,
                '--register' => true,
            ]);
        }

        return $this->repository->delete($id);
    }
}
