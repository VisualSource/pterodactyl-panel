<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2022 - 2023 Collin Blosser <cblosser@titanhosting.us>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Contracts\Repository;

interface DomainRepositoryInterface extends RepositoryInterface {
    /**
     * Return a nest or all nests and the count of eggs and servers for that nest.
     *
     * @return \Pterodactyl\Models\Domain|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithCounts(int $id = null);
}


?>