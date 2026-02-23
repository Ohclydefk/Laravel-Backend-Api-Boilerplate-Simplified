<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Products permissions
            [
                'name' => 'products.view',
                'label' => 'View Products',
                'group' => 'products',
            ],
            [
                'name' => 'products.create',
                'label' => 'Create Products',
                'group' => 'products',
            ],
            [
                'name' => 'products.update',
                'label' => 'Update Products',
                'group' => 'products',
            ],
            [
                'name' => 'products.delete',
                'label' => 'Delete Products',
                'group' => 'products',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
