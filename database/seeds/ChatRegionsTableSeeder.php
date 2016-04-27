<?php

use Uuid as Uuid;
use Carbon as Carbon;
use App\Models\ChatRegion;
use Illuminate\Database\Seeder;

class ChatRegionsTableSeeder extends Seeder
{
	/**
	 * @var \App\Models\ChatRegion
	 */
	protected $chat_region;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(ChatRegion $chat_region)
	{
		$this->chat_region = $chat_region;
	}

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $regions = [
        	[
        		'uuid'			=>	Uuid::generate()->string,
        		'name'			=>	'melb-01',
        		'location'		=>	'Melbourne',
                'server_ip'     =>  '192.168.10.10',
        		'server_uri'    =>	'http://stryve.io:3000',
                'active'        =>  true,
        		'created_at'	=>	$now,
        		'updated_at'	=>	$now
        	],
            [
                'uuid'          =>  Uuid::generate()->string,
                'name'          =>  'syd-01',
                'location'      =>  'Sydney',
                'server_ip'     =>  '192.168.10.10',
                'server_uri'    =>  'http://stryve.io:3000',
                'active'        =>  true,
                'created_at'    =>  $now,
                'updated_at'    =>  $now
            ],
            [
                'uuid'          =>  Uuid::generate()->string,
                'name'          =>  'us-west-01',
                'location'      =>  'US West',
                'server_ip'     =>  '192.168.10.10',
                'server_uri'    =>  'http://stryve.io:3000',
                'active'        =>  true,
                'created_at'    =>  $now,
                'updated_at'    =>  $now
            ],
             [
                'uuid'          =>  Uuid::generate()->string,
                'name'          =>  'us-east-01',
                'location'      =>  'US East',
                'server_ip'     =>  '192.168.10.10',
                'server_uri'    =>  'http://stryve.io:3000',
                'active'        =>  true,
                'created_at'    =>  $now,
                'updated_at'    =>  $now
            ]
        ];

        $this->chat_region->insert($regions);
    }
}
