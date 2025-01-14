<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Transformers\AssetMaintenancesTransformer;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\Company;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/**
 * This controller handles all actions related to Asset Maintenance for
 * the Snipe-IT Asset Management application.
 *
 * @version    v2.0
 */
class AssetMaintenancesController extends Controller
{


    /**
     *  Generates the JSON response for asset maintenances listing view.
     *
     * @see AssetMaintenancesController::getIndex() method that generates view
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     * @since [v1.8]
     * @return string JSON
     */
    public function index(Request $request)
    {
        $this->authorize('view', Asset::class);

        $maintenances = AssetMaintenance::select('asset_maintenances.*')->with('asset', 'asset.model', 'asset.location', 'supplier', 'asset.company', 'admin');

        if ($request->filled('search')) {
            $maintenances = $maintenances->TextSearch($request->input('search'));
        }

        if ($request->filled('asset_id')) {
            $maintenances->where('asset_id', '=', $request->input('asset_id'));
        }

        if ($request->filled('supplier_id')) {
            $maintenances->where('supplier_id', '=', $request->input('supplier_id'));
        }

        if ($request->filled('asset_maintenance_type')) {
            $maintenances->where('asset_maintenance_type', '=', $request->input('asset_maintenance_type'));
        }

/* VICONIA START */

        // If user want specific search terms
        if (Auth::user()->hasAccess('assets.maintenance_articles_read'))
        {
            if ($request->filled('ready_for_billing')) {
                $maintenances->where('ready_for_billing', '=', $request->input('ready_for_billing'));
            }

            if ($request->filled('invoice_id')) {
                $maintenances->where('invoice_id', '=', $request->input('invoice_id'));
            }
        }

/* VICONIA END */

        // Set the offset to the API call's offset, unless the offset is higher than the actual count of items in which
        // case we override with the actual count, so we should return 0 items.
        $offset = (($maintenances) && ($request->get('offset') > $maintenances->count())) ? $maintenances->count() : $request->get('offset', 0);

        // Check to make sure the limit is not higher than the max allowed
        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit'))) ? $limit = $request->input('limit') : $limit = config('app.max_results');

        $allowed_columns = [
                                'id',
                                'title',
                                'asset_maintenance_time',
                                'asset_maintenance_type',
                                'cost',
                                'start_date',
                                'completion_date',
                                'notes',
                                'asset_tag',
                                'asset_name',
                                'user_id',
                                'supplier',
                            ];
/* VICONIA START */
        if (Auth::user()->hasAccess('assets.maintenance_articles_read'))
        {
            array_push($allowed_columns,
                'internal_notes',
                'ready_for_billing',
                'invoice_id',
                'articles');
        }
 /* VICONIA END */

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $sort = in_array($request->input('sort'), $allowed_columns) ? e($request->input('sort')) : 'created_at';

        switch ($sort) {
            case 'user_id':
                $maintenances = $maintenances->OrderAdmin($order);
                break;
            case 'supplier':
                $maintenances = $maintenances->OrderBySupplier($order);
                break;
            case 'asset_tag':
                $maintenances = $maintenances->OrderByTag($order);
                break;
            case 'asset_name':
                $maintenances = $maintenances->OrderByAssetName($order);
                break;
            default:
                $maintenances = $maintenances->orderBy($sort, $order);
                break;
        }

        $total = $maintenances->count();
        $maintenances = $maintenances->skip($offset)->take($limit)->get();
        return (new AssetMaintenancesTransformer())->transformAssetMaintenances($maintenances, $total);


    }


    /**
     *  Validates and stores the new asset maintenance
     *
     * @see AssetMaintenancesController::getCreate() method for the form
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     * @since [v1.8]
     * @return string JSON
     */
    public function store(Request $request)
    {
        $this->authorize('update', Asset::class);
        // create a new model instance
        $assetMaintenance = new AssetMaintenance();
        $assetMaintenance->supplier_id = $request->input('supplier_id');
        $assetMaintenance->is_warranty = $request->input('is_warranty');
        $assetMaintenance->cost =  Helper::ParseCurrency($request->input('cost'));
        $assetMaintenance->notes = e($request->input('notes'));
/* VICONIA START */
        if (Auth::user()->hasAccess('assets.maintenance_articles'))
        {
            $assetMaintenance->internal_notes = $request->input('internal_notes');   
            $assetMaintenance->ready_for_billing = $request->input('ready_for_billing') ? 1 : 0; 
            $assetMaintenance->invoice_id = $request->input('invoice_id');   
            $assetMaintenance->articles = $request->input('articles'); 
        }
        
/* VICONIA END */
        $asset = Asset::find(e($request->input('asset_id')));

        if (! Company::isCurrentUserHasAccess($asset)) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'You cannot add a maintenance for that asset'));
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
            return response()->json(Helper::formatStandardApiResponse('success', $assetMaintenance, trans('admin/asset_maintenances/message.create.success')));

        }

        return response()->json(Helper::formatStandardApiResponse('error', null, $assetMaintenance->getErrors()));

    }

    /**
     *  Validates and stores an update to an asset maintenance
     *
     * @author  A. Gianotto <snipe@snipe.net>
     * @param int $assetMaintenanceId
     * @param int $request
     * @version v1.0
     * @since [v4.0]
     * @return string JSON
     */
    public function update(Request $request, $assetMaintenanceId = null)
    {
        $this->authorize('update', Asset::class);
        // Check if the asset maintenance exists
        $assetMaintenance = AssetMaintenance::findOrFail($assetMaintenanceId);

        if (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'You cannot edit a maintenance for that asset'));
        }

/* VICONIA START */

        // If we want to change asset_id, make sure it exists and that we have permission for the new asset
        if ($request->exists('asset_id'))
        {
            $asset = Asset::find(request('asset_id'));

            if (! Company::isCurrentUserHasAccess($asset)) {
                return response()->json(Helper::formatStandardApiResponse('error', null, 'You cannot edit a maintenance for that asset'));
            }

            $assetMaintenance->asset_id = $asset;
        }  

        // Override the asset maintenance data with the incomming parameters
        if ($request->exists('supplier_id'))        $assetMaintenance->supplier_id = $request->Input('supplier_id');
        if ($request->exists('asset_maintenance_type')) $assetMaintenance->asset_maintenance_type = $request->Input('asset_maintenance_type');

        if ($request->exists('title'))              $assetMaintenance->title = $request->Input('title');
        if ($request->exists('start_date'))         $assetMaintenance->start_date = $request->Input('start_date');
        if ($request->exists('completion_date'))    $assetMaintenance->completion_date = $request->Input('completion_date');
        if ($request->exists('is_warranty'))        $assetMaintenance->is_warranty = $request->Input('is_warranty');

        if ($request->exists('cost'))               $assetMaintenance->cost = Helper::ParseCurrency($request->input('cost'));
        if ($request->exists('notes'))              $assetMaintenance->notes = $request->Input('notes');


        if (Auth::user()->hasAccess('assets.maintenance_articles'))
        {
            if ($request->exists('internal_notes'))     $assetMaintenance->internal_notes = $request->Input('internal_notes');
            if ($request->exists('ready_for_billing'))  $assetMaintenance->ready_for_billing = $request->input('ready_for_billing') ? 1 : 0;
            if ($request->exists('invoice_id'))         $assetMaintenance->invoice_id = $request->Input('invoice_id');
            if ($request->exists('articles'))           $assetMaintenance->articles = $request->input('articles');
        }

/* VICONIA END */


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
            Helper::removeUnauthorizedMaintenanceData($assetMaintenance); // VICONIA LINE
            return response()->json(Helper::formatStandardApiResponse('success', $assetMaintenance, trans('admin/asset_maintenances/message.edit.success')));

        }

        Helper::removeUnauthorizedMaintenanceData($assetMaintenance); // VICONIA LINE
        return response()->json(Helper::formatStandardApiResponse('error', null, $assetMaintenance->getErrors()));
    }

    /**
     *  Delete an asset maintenance
     *
     * @author  A. Gianotto <snipe@snipe.net>
     * @param int $assetMaintenanceId
     * @version v1.0
     * @since [v4.0]
     * @return string JSON
     */
    public function destroy($assetMaintenanceId)
    {
        $this->authorize('update', Asset::class);
        // Check if the asset maintenance exists
        $assetMaintenance = AssetMaintenance::findOrFail($assetMaintenanceId);

        if (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'You cannot delete a maintenance for that asset'));
        }

        $assetMaintenance->delete();

        Helper::removeUnauthorizedMaintenanceData($assetMaintenance); // VICONIA LINE
        return response()->json(Helper::formatStandardApiResponse('success', $assetMaintenance, trans('admin/asset_maintenances/message.delete.success')));


    }

    /**
     *  View an asset maintenance
     *
     * @author  A. Gianotto <snipe@snipe.net>
     * @param int $assetMaintenanceId
     * @version v1.0
     * @since [v4.0]
     * @return string JSON
     */
    public function show($assetMaintenanceId)
    {
        $this->authorize('view', Asset::class);
        $assetMaintenance = AssetMaintenance::findOrFail($assetMaintenanceId);
        if (! Company::isCurrentUserHasAccess($assetMaintenance->asset)) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'You cannot view a maintenance for that asset'));
        }

        Helper::removeUnauthorizedMaintenanceData($assetMaintenance); // VICONIA LINE
        return (new AssetMaintenancesTransformer())->transformAssetMaintenance($assetMaintenance);

    }
}
