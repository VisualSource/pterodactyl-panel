<?php 

namespace Pterodactyl\Repositories\Eloquent;

use Pterodactyl\Models\Domain;
use Illuminate\Database\Eloquent\Collection;
use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;

class DomainRepository extends EloquentRepository implements DomainRepositoryInterface {

    /**
     * Return the model backing this repository
     */
    public function model(): string 
    {
        return Domain::class;
    }

    /**
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithCounts(?int $id = null): Collection|Domain
    {
        $instance = $this->getBuilder()->withCount(["server"]);
        if(!is_null($id)) {
            $instance = $instance->find($id,$this->getColumns());
            if(!$instance) {
                throw new RecordNotFoundException();
            }
            return $instance;
        }   

        return $instance->get($this->getColumns());
    }
}