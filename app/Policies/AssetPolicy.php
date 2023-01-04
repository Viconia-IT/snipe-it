<?php

namespace App\Policies;

use App\Models\User;

class AssetPolicy extends CheckoutablePermissionsPolicy
{
    protected function columnName()
    {
        return 'assets';
    }

    public function viewRequestable(User $user, Asset $asset = null)
    {
        return $user->hasAccess('assets.view.requestable');
    }

    public function audit(User $user, Asset $asset = null)
    {
        return $user->hasAccess('assets.audit');
    }

// VICONIA START
    public function editMaintenanceArticles(User $user, Asset $asset = null)
    {
        return $user->hasAccess('assets.maintenance_articles');
    }
// VICONIA END
}
