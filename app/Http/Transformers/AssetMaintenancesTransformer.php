<?php

namespace App\Http\Transformers;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use Auth; // VICONIA LINE
use Gate;
use Illuminate\Database\Eloquent\Collection;

class AssetMaintenancesTransformer
{
    public function transformAssetMaintenances(Collection $assetmaintenances, $total)
    {
        $array = [];
        foreach ($assetmaintenances as $assetmaintenance) {
            $array[] = self::transformAssetMaintenance($assetmaintenance);
        }

        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformAssetMaintenance(AssetMaintenance $assetmaintenance)
    {
        $array = [
            'id'            => (int) $assetmaintenance->id,
            'asset' => ($assetmaintenance->asset) ? [
                'id' => (int) $assetmaintenance->asset->id,
                'name'=> ($assetmaintenance->asset->name) ? e($assetmaintenance->asset->name) : null,
                'asset_tag'=> e($assetmaintenance->asset->asset_tag),

            ] : null,
            'model' => (($assetmaintenance->asset) && ($assetmaintenance->asset->model)) ? [
                'id' => (int) $assetmaintenance->asset->model->id,
                'name'=> ($assetmaintenance->asset->model->name) ? e($assetmaintenance->asset->model->name).' '.e($assetmaintenance->asset->model->model_number) : null,
            ] : null,
            'company' => (($assetmaintenance->asset) && ($assetmaintenance->asset->company)) ? [
                'id' => (int) $assetmaintenance->asset->company->id,
                'name'=> ($assetmaintenance->asset->company->name) ? e($assetmaintenance->asset->company->name) : null,

            ] : null,
            'title'         => ($assetmaintenance->title) ? e($assetmaintenance->title) : null,
            'location' => (($assetmaintenance->asset) && ($assetmaintenance->asset->location)) ? [
                'id' => (int) $assetmaintenance->asset->location->id,
                'name'=> e($assetmaintenance->asset->location->name),

            ] : null,
            'notes'         => ($assetmaintenance->notes) ? e($assetmaintenance->notes) : null,
            'supplier'      => ($assetmaintenance->supplier) ? ['id' => $assetmaintenance->supplier->id, 'name'=> e($assetmaintenance->supplier->name)] : null,
            'cost'          => Helper::formatCurrencyOutput($assetmaintenance->cost),
            'asset_maintenance_type'          => e($assetmaintenance->asset_maintenance_type),
            'start_date'         => Helper::getFormattedDateObject($assetmaintenance->start_date, 'date'),
            'asset_maintenance_time'          => $assetmaintenance->asset_maintenance_time,
            'completion_date'     => Helper::getFormattedDateObject($assetmaintenance->completion_date, 'date'),
            'user_id'    => ($assetmaintenance->admin) ? ['id' => $assetmaintenance->admin->id, 'name'=> e($assetmaintenance->admin->getFullNameAttribute())] : null,
            'created_at' => Helper::getFormattedDateObject($assetmaintenance->created_at, 'datetime'),
            'updated_at' => Helper::getFormattedDateObject($assetmaintenance->updated_at, 'datetime'),
// VICONIA START
            'is_warranty'         => $assetmaintenance->is_warranty,
        ];

        // Only add our variables if the user has access
        if (Auth::user()->hasAccess('assets.maintenance_articles_read'))
        {
            $array['internal_notes']       = ($assetmaintenance->internal_notes) ? e($assetmaintenance->internal_notes) : null;
            $array['ready_for_billing']   = $assetmaintenance->ready_for_billing;
            $array['invoice_id']         = ($assetmaintenance->invoice_id) ? e($assetmaintenance->invoice_id) : null;
            $array['articles']         = AssetMaintenance::parseArticles($assetmaintenance->articles);
        }
// VICONIA END

        $permissions_array['available_actions'] = [
            'update' => Gate::allows('update', Asset::class),
            'delete' => Gate::allows('delete', Asset::class),
        ];

        $array += $permissions_array;

        return $array;
    }
}
