<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pterodactyl\Models\Domain. 
 *  
 * @property int $id
 * @property string $domain
 * @property int|null $server_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Pterodactyl\Models\Server|null $server
 * 
 * @method static \Database\Factories\AllocationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Allocation whereUpdatedAt($value)
 * @mixin \Eloquent
 **/
class Domain extends Model {

    public const RESOURCE_NAME = "domain";

    protected $table = "domains";

    protected $guarded = ['id','created_at',"updated_at"];

    protected $casts = [
        "server_id" => 'integer'
    ];

    public static $validationRules = [
        'server_id' => 'nullable|exists:servers,id',
        'domain' => 'required|string'
    ];

    /**
     * Gets information for the server associated with this domain.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server(): BelongsTo {
        return $this->belongsTo(Server::class,"id","server_id");
    }
   
}