<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelSetting extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'channel_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the channel that owns the setting.
     *
     * @return \App\Models\Channel
     */
    public function channel()
    {
        return $this->belongsTo('App\Models\Channel');
    }

    /**
     * Creates a new channel setting
     * 
     * @param int $channel_id
     * @return this
     */
    public function createChannelSetting($channel_id, $private = false)
    {
        $channel_setting = new $this;
        $channel_setting->channel_id = $channel_id;
        $channel_setting->private = $private;
        $channel_setting->save();

        return $channel_setting;
    }

}