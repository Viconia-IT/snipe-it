<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Company;
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

        // If user can edit articles in a maintenance
        if (Auth::user()->hasAccess('assets.maintenance_articles')) //(Asset::editMaintenanceArticles(Auth::user()))
        {
            $assetMaintenance->internal_notes =     $request->input('internal_notes');
            $assetMaintenance->ready_for_billing =  $request->input('ready_for_billing') ? 1 : 0; ;
            $assetMaintenance->invoice_id =         $request->input('invoice_id');

            // Convert the received array of strings to objects with property {article_nr, component_name, component_id}
            $articleObjs = AssetMaintenance::articleStringsToObjects($request->input('articles'));

            // Check out all components that we added articles from. This stores an aditional property in our object {component_asset_id}
            // This is a reference we can use to check in the component again later
            $articleObjsToStore = [];
            $result = Helper::CheckInOutComponents($request->input('asset_id'), $articleObjs, [], $articleObjsToStore);
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

        Helper::removeUnauthorizedMaintenanceData($assetMaintenance); // VICONIA LINE

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

        /*return redirect()->back()
        ->withInput()
        ->with('error', $request->input('is_warranty'));*/

        // If user can edit articles in a maintenance
        if (Auth::user()->hasAccess('assets.maintenance_articles')) //(Asset::editMaintenanceArticles(Auth::user()))
        {
            $assetMaintenance->internal_notes =     $request->input('internal_notes');
            $assetMaintenance->ready_for_billing =  $request->input('ready_for_billing') ? 1 : 0;
            $assetMaintenance->invoice_id =         $request->input('invoice_id');

            // Convert the received array of strings to objects with property {article_nr, component_name, component_id}
            $newObjs = AssetMaintenance::articleStringsToObjects($request->input('articles'));
            $oldObjs = AssetMaintenance::parseArticles($assetMaintenance->articles);
            $articleObjsToStore = [];

            $newAssetId = $request->input('asset_id');
            $oldAssetId = $assetMaintenance->asset_id;

            //    -----  If same asset  ------   //
            if ($newAssetId == $oldAssetId) {
                $result = Helper::CheckInOutComponents($newAssetId, $newObjs, $oldObjs, $articleObjsToStore);
                if ($result["status"] == "error") {
                    return redirect()->back()
                                        ->withInput()
                                        ->with('error', $result["messages"]);
                }
            }
            //   -----  If asset changed  ------   //
            else {
                // Remove components on the current asset
                $result = Helper::CheckInOutComponents($assetMaintenance->asset_id, [],  $oldObjs);

                // Add all components to the new asset
                $result = Helper::CheckInOutComponents($request->input('asset_id'), $newObjs, [], $articleObjsToStore);
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
        $result = Helper::CheckInOutComponents($assetMaintenance->asset_id, [], $articleObjs);
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

        Helper::removeUnauthorizedMaintenanceData($assetMaintenance); // VICONIA LINE

        return view('asset_maintenances/view')->with('assetMaintenance', $assetMaintenance);
    }
}
