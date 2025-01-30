<?php

namespace Database\Seeders;
  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'product-list',
           'product-create',
           'product-edit',
           'product-delete',
           'medical-visit-list',
           'medical-visit-create',
           'medical-visit-edit',
           'medical-visit-delete'
        ];
        
        foreach ($permissions as $permission) {
             Permission::create(['name' => $permission]);
        }
    }
}