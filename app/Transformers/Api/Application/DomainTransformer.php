<?php

namespace Pterodactyl\Transformers\Api\Application;

use Pterodactyl\Models\Domain;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use Pterodactyl\Services\Acl\Api\AdminAcl;
use Pterodactyl\Transformers\Api\Transformer;

class DomainTransformer extends Transformer
{
    /**
     * List of resources that can be included.
     */
    protected array $availableIncludes = ['server'];

    /**
     * Return the resource name for the JSONAPI output.
     */
    public function getResourceName(): string
    {
        return Domain::RESOURCE_NAME;
    }

    /**
     * Return a generic transformed location array.
     */
    public function transform(Domain $model): array
    {
        return [
            'id' => $model->id,
            'domain' => $model->domain,
            'server_id' => $model->server_id,
            'created_at' => self::formatTimestamp($model->created_at),
            'updated_at' => self::formatTimestamp($model->updated_at),
        ];
    }

    /**
     * Return the server associated with this domain.
     */
    public function includeServer(Domain $domain): Item|NullResource
    {
        if (!$this->authorize(AdminAcl::RESOURCE_SERVERS)) {
            return $this->null();
        }

        $server = $domain->server;

        if (is_null($server)) {
            return $this->null();
        }

        return $this->item($server, new ServerTransformer());
    }
}
