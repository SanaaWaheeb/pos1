<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('cache:clear');

        $admin_permission = [
        ];

        $owner_permission = [
            'Manage Testimonial',
            'Create Testimonial',
            'Edit Testimonial',
            'Delete Testimonial',
        ];


        $superAdminRole  = Role::where('name', 'super admin')->first();
        foreach ($admin_permission  as $key => $value)
        {
            $permission = Permission::where('name', $value)->first();
            if(empty($permission))
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
            }
            if(!$superAdminRole->hasPermissionTo($value))
            {
                $superAdminRole->givePermissionTo($permission);
            }
        }
        // Company ..
        $role = Role::where('name', 'Owner')->where('guard_name', 'web')->exists();
        if(!$role)
        {
            $company_role        = Role::create(
                [
                    'name' => 'Owner',
                    'created_by' => 1,
                ]
            );
        }
        $company_role = Role::where('name', 'Owner')->first();
        foreach ($owner_permission as $key => $value)
        {
            $permission = Permission::where('name', $value)->first();
            if(empty($permission))
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
            }
            if(!$company_role->hasPermissionTo($value))
            {
                $company_role->givePermissionTo($permission);
            }
        }
    }
}
