<?php
// app/Config/Filters.php
// Snippet to merge into your existing CI4 Filters config: add the 'role'
// alias below to the $aliases array, and nothing else needs to change.
// (Shown as a standalone reference file - copy the relevant line into
// your project's real Config\Filters.)

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Filters\RoleFilter;

class FiltersReference extends BaseConfig
{
    // Add this line inside your real Filters.php $aliases array:
    public array $aliasesToMerge = [
        'role' => RoleFilter::class,
    ];
}
