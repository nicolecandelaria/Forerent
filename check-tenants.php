<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$manager = App\Models\User::where('role', 'manager')->first();
echo "Manager: {$manager->first_name} {$manager->last_name} (ID: {$manager->user_id})\n";

$units = App\Models\Unit::where('manager_id', $manager->user_id)->pluck('unit_id');
$beds = App\Models\Bed::whereIn('unit_id', $units)->pluck('bed_id');
$leases = App\Models\Lease::whereIn('bed_id', $beds)->where('status', 'Active')->with('tenant')->get();

echo "\nTenants under this manager:\n";
foreach ($leases as $l) {
    echo "- {$l->tenant->first_name} {$l->tenant->last_name} | email: {$l->tenant->email}\n";
}
