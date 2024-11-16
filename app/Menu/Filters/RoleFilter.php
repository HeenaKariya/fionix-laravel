<?php

namespace App\Menu\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;

class RoleFilter implements FilterInterface
{
    public function transform($item)
    {
        if (isset($item['role']) && !Auth::user()->hasRole($item['role'])) {
            return false;
        }

        return $item;
    }
}
