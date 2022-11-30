<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Repositories\Eloquent;

use Pterodactyl\Contracts\Repository\DomainRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Pterodactyl\Models\Domain;

class DomainsRepository extends EloquentRepository implements DomainRepositoryInterface {
    
    public function model(){
        return Domain::class;
    }

    public function getWithCounts(int $id = null) {
        $instance = $this->getBuilder()->with(["servers"]);

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


?>