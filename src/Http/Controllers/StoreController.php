<?php

namespace Fleetbase\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;
use Fleetbase\Models\Storefront\Store;
use Illuminate\Http\Request;

class StoreController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'stores';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';

    /**
     * Updates a record with request payload
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateRecord(Request $request)
    {
        return $this->model::updateRecordFromRequest($request, function (&$request, Store &$store, &$input) {
            $store->flushAttributesCache();
            $alertable = $request->input('store.alertable', []);

            // set alertables to public_id
            $input['alertable'] = collect($alertable)->mapWithKeys(function ($alertables, $key) {
                return [$key => collect($alertables)->map(function ($user) {
                    return $user['public_id'];
                })->values()->toArray()];
            })->toArray();
        });
    }
}
