<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\Region;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionsController extends Controller
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
	 * Returns all available regions
	 *
	 * @GET("/api/regions")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={"regions": { ... } })
	 */
    public function index()
    {
    	// get all regions
    	$regions = $this->region->get()->toArray();

    	// return with HTTP 200 OK along with access token
		return Larapi::respondOk($regions);
    }


    /**
	 * Creates a new region
	 *
	 * @POST("/api/regions")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={"region": { ... } })
	 */
    public function store()
    {
    	// TODO:: create region
    }


    /**
	 * Updates a region
	 *
	 * @POST("/api/regions/{uuid}")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @PathParams({
	 *      @Parameter("uuid", description="The regions unique identifier")
	 * })
	 * @Response(200, body={"region": { ... } })
	 */
    public function update($uuid)
    {
    	// TODO:: update region
    }


    /**
	 * Deletes a region
	 *
	 * @POST("/api/regions/{uuid}")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @PathParams({
	 *      @Parameter("uuid", description="The regions unique identifier")
	 * })
	 * @Response(200, body={ })
	 */
    public function delete($uuid)
    {
    	// TODO:: delete region
    }
}
