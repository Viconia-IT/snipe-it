<?php

namespace App\Http\Controllers\Components;

use App\Helpers\Helper; // VICONIA LINE
use App\Events\CheckoutableCheckedOut;
use App\Events\ComponentCheckedOut;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  // VICONIA LINE
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ComponentCheckoutController extends Controller
{
    /**
     * Returns a view that allows the checkout of a component to an asset.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentCheckoutController::store() method that stores the data.
     * @since [v3.0]
     * @param int $componentId
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($componentId)
    {
        // Check if the component exists
        if (is_null($component = Component::find($componentId))) {
            // Redirect to the component management page with error
            return redirect()->route('components.index')->with('error', trans('admin/components/message.not_found'));
        }
        $this->authorize('checkout', $component);

        return view('components/checkout', compact('component'));
    }

    /**
     * Validate and store checkout data.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ComponentCheckoutController::create() method that returns the form.
     * @since [v3.0]
     * @param Request $request
     * @param int $componentId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, $componentId)
    {
        // Check if the component exists
        if (is_null($component = Component::find($componentId))) {
            // Redirect to the component management page with error
            return redirect()->route('components.index')->with('error', trans('admin/components/message.not_found'));
        }

        $this->authorize('checkout', $component);

        $max_to_checkout = $component->numRemaining();
        $validator = Validator::make($request->all(), [
            'asset_id'          => 'required',
            'assigned_qty'      => "required|numeric|between:1,$max_to_checkout",
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin_user = Auth::user();
        $asset_id = e($request->input('asset_id'));

        // Check if the user exists
        if (is_null($asset = Asset::find($asset_id))) {
            // Redirect to the component management page with error
            return redirect()->route('components.index')->with('error', trans('admin/components/message.asset_does_not_exist'));
        }

        // Update the component data
        $component->asset_id = $asset_id;

        $component->assets()->attach($component->id, [
            'component_id' => $component->id,
            'user_id' => $admin_user->id,
            'created_at' => date('Y-m-d H:i:s'),
            'assigned_qty' => $request->input('assigned_qty'),
            'asset_id' => $asset_id,
            'note' => $request->input('note'),
        ]);

        event(new CheckoutableCheckedOut($component, $asset, Auth::user(), $request->input('note')));

        return redirect()->route('components.index')->with('success', trans('admin/components/message.checkout.success'));
    }

// VICONIA START
    /**
     * Validate and store checkout data.
     *
     * @author [C. Pettersson] [Viconia]
     * @param {asset_id: assetID, assigned_qty: checkoutAmount, note: notes}
     * @param int $componentId
     * @return {status: success/error, payload: components_assets id/null, messages: error message}
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public static function internal_store($componentId, $asset_id, $assigned_qty, $note)
    {
        // Check if the component exists
        if (is_null($component = Component::find($componentId))) {
            return Helper::formatStandardApiResponse('error', null, "Component doesn't exist");
        }

        // The permission check is done outside this function
        //$context->authorize('checkout', $component);

        if ($assigned_qty > $component->numRemaining()) {
            return Helper::formatStandardApiResponse('error', null, "Not enough components left");
        }


        $admin_user = Auth::user();

        // Check if the asset exists
        if (is_null($asset = Asset::find($asset_id))) {
            return Helper::formatStandardApiResponse('error', null, "Asset doesn't exist");
        }

        // Update the component data
        $component->asset_id = $asset_id; // Why is this needed?

        $component->assets()->attach($component->id, [
            'component_id' => $component->id,
            'user_id' => $admin_user->id,
            'created_at' => date('Y-m-d H:i:s'),
            'assigned_qty' => $assigned_qty,
            'asset_id' => $asset_id,
            'note' => $note,
        ]);

        // Get the ID of the new checkout so we can check it in later if needed
        $returnID = DB::table('components_assets')->latest('id')->first()->id;

        // Logg the event
        event(new CheckoutableCheckedOut($component, $asset, Auth::user(), $note));

        return Helper::formatStandardApiResponse('success', $returnID,  trans('admin/components/message.checkout.success'));
    }
// VICONIA END
}
