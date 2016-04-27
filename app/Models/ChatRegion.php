<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatRegion extends Model
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
     * Get chat servers belonging to this region
     *
     * @return \App\Models\ChatServer
     */
    public function chat_servers()
    {
        return $this->hasMany('App\Models\ChatServer', 'region_id', 'id');
    }

    /**
     * Return the chat region
     * 
     * @param string $name
     * @return this 
     */
    public function getChatRegionByName($name)
    {
        return $this->where('name', strtolower($name))->first();
    }
}
