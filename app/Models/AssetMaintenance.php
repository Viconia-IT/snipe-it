<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

/**
 * Model for Asset Maintenances.
 *
 * @version    v1.0
 */
class AssetMaintenance extends Model implements ICompanyableChild
{
    use HasFactory;
    use SoftDeletes;
    use CompanyableChildTrait;
    use ValidatingTrait;
    protected $casts = [
        'start_date' => 'datetime',
        'completion_date' => 'datetime',
    ];
    protected $table = 'asset_maintenances';
    protected $rules = [
        'asset_id'               => 'required|integer',
        'supplier_id'            => 'required|integer',
        'asset_maintenance_type' => 'required',
        'title'                  => 'required|max:100',
        'is_warranty'            => 'boolean',
        'start_date'             => 'required|date',
        'completion_date'        => 'nullable|date',
        'notes'                  => 'string|nullable',
        'cost'                   => 'numeric|nullable',
/* VICONIA START */
        'internal_notes'         => 'string|nullable',
        'ready_for_billing'      => 'boolean', 
        'invoice_id'             => 'string|nullable',
        'articles'               => 'string|nullable', 
/* VICONIA END */  
    ];

    use Searchable;

    /**
     * The attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableAttributes = ['title', 'notes', 'asset_maintenance_type', 'cost', 'start_date', 'completion_date', 'invoice_id', 'articles']; /* VICONIA ARGUMENTS 'invoice_id', 'articles' */  

    /**
     * The relations and their attributes that should be included when searching the model.
     *
     * @var array
     */
    protected $searchableRelations = [
        'asset'     => ['name', 'asset_tag'],
        'asset.model'     => ['name', 'model_number'],
    ];

    public function getCompanyableParents()
    {
        return ['asset'];
    }

    /**
     * getImprovementOptions
     *
     * @return array
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     */
    public static function getImprovementOptions()
    {
        return [
            trans('admin/asset_maintenances/general.maintenance') => trans('admin/asset_maintenances/general.maintenance'),
            trans('admin/asset_maintenances/general.repair')      => trans('admin/asset_maintenances/general.repair'),
            trans('admin/asset_maintenances/general.upgrade')     => trans('admin/asset_maintenances/general.upgrade'),
            trans('admin/asset_maintenances/general.pat_test')     => trans('admin/asset_maintenances/general.pat_test'),
            trans('admin/asset_maintenances/general.calibration')     => trans('admin/asset_maintenances/general.calibration'),
            trans('admin/asset_maintenances/general.software_support')      => trans('admin/asset_maintenances/general.software_support'),
            trans('admin/asset_maintenances/general.hardware_support')      => trans('admin/asset_maintenances/general.hardware_support'),
            trans('admin/asset_maintenances/general.configuration_change')     => trans('admin/asset_maintenances/general.configuration_change'),
        ];
    }

/* VICONIA START */

    // Takes in an array of strings
    // Returns an array of objects containing:
    // article_nr, component_id and component_name
    public static function articleStringsToObjects($articles)
    {
        if ($articles == null) return null;
        
        $array = [];
        foreach ($articles as $value)
        {
            if ($value == null || $value == "")
                continue;

            // The format of the article name is: ArticleNr - ComponentName (Component ID)
            // We want to put them in variables
            $temp = explode(" - ", $value, 2);
            if (count($temp) != 2)
                continue;

            $articleNr = $temp[0];

            $temp2 = explode(" (", $temp[1]);
            $componentID = $temp2[count($temp2)-1]; // get last index
            $componentID = substr($componentID, 0, -1); // remove the ending ')'
            $componentName = substr($temp[1], 0, -(strlen($componentID) + 3)); // remove the ending ' (Component ID)'

            $obj = (object) [
                'article_nr' => $articleNr,
                'component_id' => $componentID,
                'component_name' => $componentName,
            ];
            
            array_push($array, $obj);
        }
        return $array; //json_encode($array);
    }


    // Input an array of article objects to get an array of strings like this: ArticleNr - ComponentName (Component ID)
    public static function articleObjectsToStrings($articles)
    {
        if ($articles == null) return null;
        
        $array = [];
        foreach ($articles as $value)
        {
            if ($value == null || $value == "" || !is_object($value))
                continue;

            if (!property_exists($value, "article_nr") ||
                !property_exists($value, "component_name") || 
                !property_exists($value, "component_id") )
                continue;

            // Make a string like this: ArticleNr - ComponentName (Component ID)
            $string = $value->article_nr . " - " . $value->component_name . " (" . $value->component_id . ")";
            array_push($array, $string);
        }
        return $array;
    }


    public static function parseArticles($obj)
    {
        return $obj ? json_decode($obj) : null;
    }


    public static function serializeArticles($string)
    {
        return $string ? json_encode($string) : null;
    }
/* VICONIA END */ 

    public function setIsWarrantyAttribute($value)
    {
        if ($value == '') {
            $value = 0;
        }
        $this->attributes['is_warranty'] = $value;
    }

    /**
     * @param $value
     */
    public function setCostAttribute($value)
    {
        $value = Helper::ParseFloat($value);
        if ($value == '0.0') {
            $value = null;
        }
        $this->attributes['cost'] = $value;
    }

    /**
     * @param $value
     */
    public function setNotesAttribute($value)
    {
        if ($value == '') {
            $value = null;
        }
        $this->attributes['notes'] = $value;
    }

    /**
     * @param $value
     */
    public function setCompletionDateAttribute($value)
    {
        if ($value == '' || $value == '0000-00-00') {
            $value = null;
        }
        $this->attributes['completion_date'] = $value;
    }

    /**
     * asset
     * Get asset for this improvement
     *
     * @return mixed
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     */
    public function asset()
    {
        return $this->belongsTo(\App\Models\Asset::class, 'asset_id')
                    ->withTrashed();
    }

    /**
     * Get the admin who created the maintenance
     *
     * @return mixed
     * @author  A. Gianotto <snipe@snipe.net>
     * @version v3.0
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id')
            ->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id')
                    ->withTrashed();
    }

    /**
     * -----------------------------------------------
     * BEGIN QUERY SCOPES
     * -----------------------------------------------
     **/

    /**
     * Query builder scope to order on a supplier
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  string                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderBySupplier($query, $order)
    {
        return $query->leftJoin('suppliers as suppliers_maintenances', 'asset_maintenances.supplier_id', '=', 'suppliers_maintenances.id')
            ->orderBy('suppliers_maintenances.name', $order);
    }

    /**
     * Query builder scope to order on admin user
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  string                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderAdmin($query, $order)
    {
        return $query->leftJoin('users', 'asset_maintenances.user_id', '=', 'users.id')
            ->orderBy('users.first_name', $order)
            ->orderBy('users.last_name', $order);
    }

    /**
     * Query builder scope to order on asset tag
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  string                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderByTag($query, $order)
    {
        return $query->leftJoin('assets', 'asset_maintenances.asset_id', '=', 'assets.id')
            ->orderBy('assets.asset_tag', $order);
    }

    /**
     * Query builder scope to order on asset tag
     *
     * @param  \Illuminate\Database\Query\Builder  $query  Query builder instance
     * @param  string                              $order       Order
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */
    public function scopeOrderByAssetName($query, $order)
    {
        return $query->leftJoin('assets', 'asset_maintenances.asset_id', '=', 'assets.id')
            ->orderBy('assets.name', $order);
    }
}
