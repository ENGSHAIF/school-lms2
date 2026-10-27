<?php

require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\PermissionGroup;
use Illuminate\Contracts\Console\Kernel;

$group = PermissionGroup::where('slug', 'supervisors')->first();
if ($group) {
    $perms = $group->permissions ?? [];
    if (! in_array('students.revoke_approval', $perms, true)) {
        $perms[] = 'students.revoke_approval';
        $group->permissions = array_values(array_unique($perms));
        $group->save();
        echo "Updated supervisors group with students.revoke_approval!\n";
    } else {
        echo "Supervisors group already has students.revoke_approval!\n";
    }
} else {
    echo "Supervisors group not found!\n";
}
