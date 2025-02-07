<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class MenuFilter implements FilterInterface
{
    public function transform($item)
    {
        if (isset($item['permission']) && !auth()->user()->hasPermissionTo($item['permission'])) {
            return null; // Return null to hide the item
        }
        \Log::debug($item);
        return $item;
    }
}