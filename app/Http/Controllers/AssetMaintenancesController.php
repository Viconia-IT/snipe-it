<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Company;
use App\Models\Component; // VICONIA LINE
use App\Http\Controllers\Components\ComponentCheckoutController; // VICONIA LINE
use App\Http\Controllers\Components\ComponentCheckinController; // VICONIA LINE
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Slack;
use Str;
use TCPDF;
use View;

/**
 * This controller handles all actions related to Asset Maintenance for
 * the Snipe-IT Asset Management application.
 *
 * @version    v2.0
 */
class AssetMaintenancesController extends Controller
{
    /**
    * Checks for permissions for this action.
    *
    * @todo This should be replaced with middleware and/or policies
    * @author  Vincent Sposato <vincent.sposato@gmail.com>
    * @version v1.0
    * @since [v1.8]
    * @return View
    */
    private static function getInsufficientPermissionsRedirect()
    {
        return redirect()->route('maintenances.index')
          ->with('error', trans('general.insufficient_permissions'));
    }

    /**
    *  Returns a view that invokes the ajax tables which actually contains
    * the content for the asset maintenances listing, which is generated in getDatatable.
    *
    * @todo This should be replaced with middleware and/or policies
    * @see AssetMaintenancesController::getDatatable() method that generates the JSON response
    * @author  Vincent Sposato <vincent.sposato@gmail.com>
    * @version v1.0
    * @since [v1.8]
    * @return View
    */
    public function index()
    {
        $this->authorize('view', Asset::class);
        return view('asset_maintenances/index');
    }

    /**
     *  Returns a form view to create a new asset maintenance.
     *
     * @see AssetMaintenancesController::postCreate() method that stores the data
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     * @since [v1.8]
     * @return mixed
     */
    public function create()
    {
        $this->authorize('update', Asset::class);
        $asset = null;

        if ($asset = Asset::find(request('asset_id'))) {
            // We have to set this so that the correct property is set in the select2 ajax dropdown
            $asset->asset_id = $asset->id;
        }

        // Prepare Asset Maintenance Type List
        $assetMaintenanceType = [
                                    '' => 'Select an asset maintenance type',
                                ] + AssetMaintenance::getImprovementOptions();
        // Mark the selected asset, if it came in

        return view('asset_maintenances/edit')
                   ->with('asset', $asset)
                   ->with('assetMaintenanceType', $assetMaintenanceType) 
                   ->with('item', new AssetMaintenance);
    }


// VICONIA START
    // Used to check out or in components when adding or removing articles on a maintenance
    public function CheckInOutComponents($asset_id, $newSet = [], $oldSet = [], & $returnArray = [])
    {
        $newSet = $newSet ? $newSet : [];
        $oldSet = $oldSet ? $oldSet : [];

        $newUnique = array_unique($newSet, SORT_REGULAR);
        $oldUnique = []; //array_unique($oldSet, SORT_REGULAR); Not working since all have different component_asset_id

        // Find objects with unique component_id
        foreach ($oldSet as $obj)
        {
            $isUnique = true;
            foreach ($oldUnique as $i)
            {
                if ($obj->component_id == $i->component_id) // If the same object values
                {
                    $isUnique = false;
                    break;
                }
            }

            if ($isUnique)
                array_push($oldUnique, $obj); // Add unique object
        }


        // Find how many of each component are in the NEW set
        foreach ($newUnique as $obj)
        {
            $obj->count = 0;
            foreach ($newSet as $i)
            {
                if ($obj->component_id == $i->component_id) // If the same object values
                {
                    $obj->count++;
                }
            }
        }

        // Find how many of each component are in the OLD set
        foreach ($oldUnique as $obj)
        {
            $obj->count = 0;
            $obj->db_refs = [];
            foreach ($oldSet as $i)
            {
                if ($obj->component_id == $i->component_id) // If the same object values
                {
                    $obj->count++;
                    array_push($obj->db_refs, $i->component_asset_id); // Save the database refs
                }
            }
        }


        // Find how many old we need to remove
        foreach ($oldUnique as $old)
        {
            $old->to_remove = $old->count;
            foreach ($newUnique as $new)
            {
                if ($old->component_id == $new->component_id)
                {
                    $old->to_remove = $old->count - $new->count;
                    break;
                }
            }
        }

        // Find how many new we need to add
        foreach ($newUnique as $new)
        {
            $new->to_add = $new->count;
            foreach ($oldUnique as $old)
            {
                if ($old->component_id == $new->component_id)
                {
                    $new->to_add = $new->count - $old->count;
                    break;
                }
            }
        }


        // First we make sure we can check in all our components in the article objects
        foreach ($newUnique as $obj)
        {
            $add_count = $obj->to_add;

            // Then make sure the component have enough quantity
            if (is_null($component = Component::find($obj->component_id))) {
                return Helper::formatStandardApiResponse('error', null, "Component doesn't exist");
            }
    
            if ($add_count > $component->numRemaining()) {
                $msg = "Not enough components (articles) left for " . $obj->component_name . " (" . $obj->component_id . "). Desired quantity: " . $add_count . " Remaining quantity: " . $component->numRemaining();
                return Helper::formatStandardApiResponse('error', null, $msg);
            }
        }


        // We are now sure we have enough component quantity
        // Now we can check out all components
        foreach ($newUnique as $obj)
        {
            for ($i=0; $i < $obj->to_add; $i++)
            { 
                $result = ComponentCheckoutController::internal_store($obj->component_id, $asset_id, 1, "Automatic from maintenance article");
                if ($result["status"] == "error") {
                    return Helper::formatStandardApiResponse('error', null, $obj->component_name . " (" . $obj->component_id . ") - " . $result["messages"]);
                }

                // Add the new object to the return array
                $newObj = new \stdClass();
                $newObj->article_nr = $obj->article_nr;
                $newObj->component_id = $obj->component_id;
                $newObj->component_name = $obj->component_name;
                $newObj->component_asset_id = $result["payload"];
                array_push($returnArray, $newObj);
            }
        }

        // Check in removed components and keep the others
        foreach ($oldUnique as $obj)
        {
            for ($i=0; $i < $obj->count; $i++)
            {
                // First get a unique component_asset_id
                $component_asset_id = array_pop($obj->db_refs);
                if ($component_asset_id == null)    continue;
                
                
                if ($obj->to_remove > 0)
                {
                    $obj->to_remove--;

                    // Ignore errors
                    // if component already have been manually checked in this function will return an error, but it's ok to continue
                    $result = ComponentCheckinController::internal_store($component_asset_id, 1, "Automatic from maintenance article");
                    /*if ($result["status"] == "error") {
                        return Helper::formatStandardApiResponse('error', null, $obj->component_name . " (" . $obj->component_id . ") - " . $result["messages"]);
                    }*/
                }
                else
                {
                    // Add the old object to the return array
                    $oldObj = new \stdClass();
                    $oldObj->article_nr = $obj->article_nr;
                    $oldObj->component_id = $obj->component_id;
                    $oldObj->component_name = $obj->component_name;
                    $oldObj->component_asset_id = $component_asset_id;
                    array_push($returnArray, $oldObj);
                }

            }            
        }

        return Helper::formatStandardApiResponse('success', null, "Successfully checked in and out all components");
    }
// VICONIA END


    /**
    *  Validates and stores the new asset maintenance
    *
    * @see AssetMaintenancesController::getCreate() method for the form
    * @author  Vincent Sposato <vincent.sposato@gmail.com>
    * @version v1.0
    * @since [v1.8]
    * @return mixed
    */
    public function store(Request $request)
    {
        $this->authorize('update', Asset::class);
        // create a new model instance
        $assetMaintenance = new AssetMaintenance();
        $assetMaintenance->supplier_id = $request->input('supplier_id');
        $assetMaintenance->is_warranty = $request->input('is_warranty');
        $assetMaintenance->cost = Helper::ParseCurrency($request->input('cost'));
        $assetMaintenance->notes = $request->input('notes');
/* VICONIA START */
        $assetMaintenance->invoice_id = $request->input('invoice_id');

        // If user can edit articles in a maintenance
        if (Auth::user()->hasAccess('assets.maintenance_articles')) //(Asset::editMaintenanceArticles(Auth::user()))
        {
            // Convert the received array of strings to objects with property {article_nr, component_name, component_id}
            $articleObjs = AssetMaintenance::articleStringsToObjects($request->input('articles'));

            // Check out all components that we added articles from. This stores an aditional property in our object {component_asset_id}
            // This is a reference we can use to check in the component again later
            $articleObjsToStore = [];
            $result = $this->CheckInOutComponents($request->input('asset_id'), $articleObjs, [], $articleObjsToStore);
            if ($result["status"] == "error") {
                return redirect()->back()
                                ->withInput()
                                ->with('error', $result["messages"]);
            }

            // Convert the array of JSON objects to a single string for storing in DB
            $assetMaintenance->articles = AssetMaintenance::serializeArticles($articleObjsToStore);
        }
/* VICONIA END */

        $asset = Asset::find($request->input('asset_id'));

        if ((! Company::isCurrentUserHasAccess($asset)) && ($asset != null)) {
            return static::getInsufficientPermissionsRedirect();
        }

        // Save the asset maintenance data
        $assetMaintenance->asset_id = $request->input('asset_id');
        $assetMaintenance->asset_maintenance_type = $request->input('asset_maintenance_type');
        $assetMaintenance->title = $request->input('title');
        $assetMaintenance->start_date = $request->input('start_date');
        $assetMaintenance->completion_date = $request->input('completion_date');
        $assetMaintenance->user_id = Auth::id();

        if (($assetMaintenance->completion_date !== null)
            && ($assetMaintenance->start_date !== '')
            && ($assetMaintenance->start_date !== '0000-00-00')
        ) {
            $startDate = Carbon::parse($assetMaintenance->start_date);
            $completionDate = Carbon::parse($assetMaintenance->completion_date);
            $assetMaintenance->asset_maintenance_time = $completionDate->diffInDays($startDate);
        }

        // Was the asset maintenance created?
        if ($assetMaintenance->save()) {
            // Redirect to the new asset maintenance page
            return redirect()->route('maintenances.index')
                           ->with('success', trans('admin/asset_maintenances/message.create.success'));
        }

        return redirect()->back()->withInput()->withErrors($assetMaintenance->getErrors());
    }

    /**
    *  Returns a form view to edit a selected asset maintenance.
    *
    * @see AssetMaintenancesController::postEdit() method that stores the data
    * @author  Vincent Sposato <vincent.sposato@gmail.com>
    * @param int $assetMaintenanceId
    * @version v1.0
    * @since [v1.8]
    * @return mixed
    */
    public function edit($assetMaintenanceId = null)
    {
        $this->authorize('update', Asset::class);
        // Check if the asset maintenance exists
        if (is_null($assetMaintenance = AssetMaintenance::find($assetMaintenanceId))) {
            // Redirect to the improvement management page
            return redirect()->route('maintenances.index')
                           ->with('error', trans('admin/asset_maintenances/message.not_found'));
        } elseif (! $assetMaintenance->asset) {
            return redirect()->route('maintenances.index')
                ->with('error', 'The asset associated with this maintenance does not exist.');
        } elseif (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return static::getInsufficientPermissionsRedirect();
        }

        if ($assetMaintenance->completion_date == '0000-00-00') {
            $assetMaintenance->completion_date = null;
        }

        if ($assetMaintenance->start_date == '0000-00-00') {
            $assetMaintenance->start_date = null;
        }

        if ($assetMaintenance->cost == '0.00') {
            $assetMaintenance->cost = null;
        }

        // Prepare Improvement Type List
        $assetMaintenanceType = [
                                    '' => 'Select an improvement type',
                                ] + AssetMaintenance::getImprovementOptions();

        // Get Supplier List
        // Render the view
        return view('asset_maintenances/edit')
                   ->with('selectedAsset', null)
                   ->with('assetMaintenanceType', $assetMaintenanceType)
                   ->with('item', $assetMaintenance);
    }

    /**
     *  Validates and stores an update to an asset maintenance
     *
     * @see AssetMaintenancesController::postEdit() method that stores the data
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @param Request $request
     * @param int $assetMaintenanceId
     * @return mixed
     * @version v1.0
     * @since [v1.8]
     */
    public function update(Request $request, $assetMaintenanceId = null)
    {
        $this->authorize('update', Asset::class);
        // Check if the asset maintenance exists
        if (is_null($assetMaintenance = AssetMaintenance::find($assetMaintenanceId))) {
            // Redirect to the asset maintenance management page
            return redirect()->route('maintenances.index')
                           ->with('error', trans('admin/asset_maintenances/message.not_found'));
        } elseif (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return static::getInsufficientPermissionsRedirect();
        }

/* VICONIA START */
        $asset = Asset::find(request('asset_id'));
        if (! Company::isCurrentUserHasAccess($asset)) {
            return static::getInsufficientPermissionsRedirect();
        }

        // If user can edit articles in a maintenance
        if (Auth::user()->hasAccess('assets.maintenance_articles')) //(Asset::editMaintenanceArticles(Auth::user()))
        {
            // Convert the received array of strings to objects with property {article_nr, component_name, component_id}
            $newObjs = AssetMaintenance::articleStringsToObjects($request->input('articles'));
            $oldObjs = AssetMaintenance::parseArticles($assetMaintenance->articles);
            $articleObjsToStore = [];

            $newAssetId = $request->input('asset_id');
            $oldAssetId = $assetMaintenance->asset_id;

            //    -----  If same asset  ------   //
            if ($newAssetId == $oldAssetId) {
                $result = $this->CheckInOutComponents($newAssetId, $newObjs, $oldObjs, $articleObjsToStore);
                if ($result["status"] == "error") {
                    return redirect()->back()
                                        ->withInput()
                                        ->with('error', $result["messages"]);
                }
            }
            //   -----  If asset changed  ------   //
            else {
                // Remove components on the current asset
                $result = $this->CheckInOutComponents($assetMaintenance->asset_id, [],  $oldObjs);

                // Add all components to the new asset
                $result = $this->CheckInOutComponents($request->input('asset_id'), $newObjs, [], $articleObjsToStore);
                if ($result["status"] == "error") {
                    return redirect()->back()
                                    ->withInput()
                                    ->with('error', $result["messages"]);
                }
            }

            // Convert the array of JSON objects to a single string for storing in DB
            $assetMaintenance->articles = AssetMaintenance::serializeArticles($articleObjsToStore); 
        }

        $assetMaintenance->supplier_id = $request->input('supplier_id');
        $assetMaintenance->is_warranty = $request->input('is_warranty');
        $assetMaintenance->cost =  Helper::ParseCurrency($request->input('cost'));
        $assetMaintenance->notes = $request->input('notes');
        $assetMaintenance->invoice_id = $request->input('invoice_id');
        
/* VICONIA END */

        // Save the asset maintenance data
        $assetMaintenance->asset_id = $request->input('asset_id');
        $assetMaintenance->asset_maintenance_type = $request->input('asset_maintenance_type');
        $assetMaintenance->title = $request->input('title');
        $assetMaintenance->start_date = $request->input('start_date');
        $assetMaintenance->completion_date = $request->input('completion_date');

        if (($assetMaintenance->completion_date == null)
        ) {
            if (($assetMaintenance->asset_maintenance_time !== 0)
              || (! is_null($assetMaintenance->asset_maintenance_time))
            ) {
                $assetMaintenance->asset_maintenance_time = null;
            }
        }

        if (($assetMaintenance->completion_date !== null)
          && ($assetMaintenance->start_date !== '')
          && ($assetMaintenance->start_date !== '0000-00-00')
        ) {
            $startDate = Carbon::parse($assetMaintenance->start_date);
            $completionDate = Carbon::parse($assetMaintenance->completion_date);
            $assetMaintenance->asset_maintenance_time = $completionDate->diffInDays($startDate);
        }

      // Was the asset maintenance created?
        if ($assetMaintenance->save()) {

            // Redirect to the new asset maintenance page
            return redirect()->route('maintenances.index')
                         ->with('success', trans('admin/asset_maintenances/message.edit.success'));
        }

        return redirect()->back()->withInput()->withErrors($assetMaintenance->getErrors());
    }

    /**
    *  Delete an asset maintenance
    *
    * @author  Vincent Sposato <vincent.sposato@gmail.com>
    * @param int $assetMaintenanceId
    * @version v1.0
    * @since [v1.8]
    * @return mixed
    */
    public function destroy($assetMaintenanceId)
    {
        $this->authorize('update', Asset::class);
        // Check if the asset maintenance exists
        if (is_null($assetMaintenance = AssetMaintenance::find($assetMaintenanceId))) {
            // Redirect to the asset maintenance management page
            return redirect()->route('maintenances.index')
                           ->with('error', trans('admin/asset_maintenances/message.not_found'));
        } elseif (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return static::getInsufficientPermissionsRedirect();
        }

// VICONIA START

        // Checkin all components/articles we have
        $articleObjs = AssetMaintenance::parseArticles($assetMaintenance->articles); 
        $result = $this->CheckInOutComponents($assetMaintenance->asset_id, [], $articleObjs);
        if ($result["status"] == "error") {
            return redirect()->route('maintenances.index')
                             ->with('error', $result["messages"]);
        }

// VICONIA END

        // Delete the asset maintenance
        $assetMaintenance->delete();

        // Redirect to the asset_maintenance management page
        return redirect()->route('maintenances.index')
                       ->with('success', trans('admin/asset_maintenances/message.delete.success'));
    }

    /**
    *  View an asset maintenance
    *
    * @author  Vincent Sposato <vincent.sposato@gmail.com>
    * @param int $assetMaintenanceId
    * @version v1.0
    * @since [v1.8]
    * @return View
    */
    public function show($assetMaintenanceId)
    {
        $this->authorize('view', Asset::class);

        // Check if the asset maintenance exists
        if (is_null($assetMaintenance = AssetMaintenance::find($assetMaintenanceId))) {
            // Redirect to the asset maintenance management page
            return redirect()->route('maintenances.index')
                           ->with('error', trans('admin/asset_maintenances/message.not_found'));
        } elseif (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return static::getInsufficientPermissionsRedirect();
        }

        return view('asset_maintenances/view')->with('assetMaintenance', $assetMaintenance);
    }
}
