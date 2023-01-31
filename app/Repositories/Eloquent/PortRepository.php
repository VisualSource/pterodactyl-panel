<?php

namespace Pterodactyl\Repositories\Eloquent;

use Pterodactyl\Models\Port;
use Illuminate\Database\Eloquent\Collection;
use Pterodactyl\Contracts\Repository\PortRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;

class PortRepository extends EloquentRepository implements PortRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return Port::class;
    }

    /**
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithAllocations(?int $id = null): Collection|Port
    {
        $instance = $this->getBuilder()->with(['allocation']);
        if (!is_null($id)) {
            $instance = $instance->find($id, $this->getColumns());
            if (!$instance) {
                throw new RecordNotFoundException();
            }

            return $instance;
        }

        return $instance->get($this->getColumns());
    }
}
