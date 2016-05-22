<?php

use Uuid as Uuid;
use Carbon as Carbon;
use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{
	/**
	 * @var \App\Models\Region
	 */
	protected $region;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Region $region)
	{
		$this->region = $region;
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
                'server_ip'     =>  config('stryve.default_ip'),
        		'server_uri'    =>	config('stryve.defallt_url'),
                'active'        =>  true,
        		'created_at'	=>	$now,
        		'updated_at'	=>	$now
        	],
            [
                'uuid'          =>  Uuid::generate()->string,
                'name'          =>  'syd-01',
                'location'      =>  'Sydney',
                'server_ip'     =>  config('stryve.default_ip'),
                'server_uri'    =>  config('stryve.defallt_url'),
                'active'        =>  true,
                'created_at'    =>  $now,
                'updated_at'    =>  $now
            ],
            [
                'uuid'          =>  Uuid::generate()->string,
                'name'          =>  'us-west-01',
                'location'      =>  'US West',
                'server_ip'     =>  config('stryve.default_ip'),
                'server_uri'    =>  config('stryve.defallt_url'),
                'active'        =>  true,
                'created_at'    =>  $now,
                'updated_at'    =>  $now
            ],
             [
                'uuid'          =>  Uuid::generate()->string,
                'name'          =>  'us-east-01',
                'location'      =>  'US East',
                'server_ip'     =>  config('stryve.default_ip'),
                'server_uri'    =>  config('stryve.defallt_url'),
                'active'        =>  true,
                'created_at'    =>  $now,
                'updated_at'    =>  $now
            ]
        ];

        $this->region->insert($regions);
    }
}
