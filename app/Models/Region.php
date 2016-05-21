<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
	use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'active', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the servers belonging to this region
     *
     * @return \App\Models\Server
     */
    public function servers()
    {
        return $this->hasMany('App\Models\Server', 'region_id', 'id');
    }

    /**
     * Return the region
     * 
     * @param string $name
     * @return this 
     */
    public function getRegionByName($name)
    {
        return $this->where('name', strtolower($name))->first();
    }
}
