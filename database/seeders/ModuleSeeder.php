<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $modules = [
            ['name' => 'user_management', 'display_name' => 'User Management', 'icon' => 'fas fa-users-cog', 'is_active' => true],
            ['name' => 'warehouse_management', 'display_name' => 'Warehouse Management', 'icon' => 'fas fa-warehouse', 'is_active' => true],
            ['name' => 'view_blocks', 'display_name' => 'View Blocks', 'icon' => 'fas fa-th-large', 'is_active' => true],
            ['name' => 'materials', 'display_name' => 'Materials', 'icon' => 'fas fa-cube', 'is_active' => true],
            ['name' => 'vendor_management', 'display_name' => 'Vendor Management', 'icon' => 'fas fa-truck', 'is_active' => true],
            ['name' => 'quality_analysis', 'display_name' => 'Quality Analysis', 'icon' => 'fas fa-check-circle', 'is_active' => true],
            ['name' => 'purchase_orders', 'display_name' => 'Purchase Orders', 'icon' => 'fas fa-shopping-cart', 'is_active' => true],
            ['name' => 'inventory_control', 'display_name' => 'Inventory Control', 'icon' => 'fas fa-boxes', 'is_active' => true],
            ['name' => 'barcode_management', 'display_name' => 'Barcode Management', 'icon' => 'fas fa-qrcode', 'is_active' => true],
            ['name' => 'report_analysis', 'display_name' => 'Report Analysis', 'icon' => 'fas fa-file-alt', 'is_active' => true],
            ['name' => 'machines', 'display_name' => 'Machines', 'icon' => 'fas fa-cogs', 'is_active' => true],
            ['name' => 'work_orders', 'display_name' => 'Work Orders', 'icon' => 'fas fa-tasks', 'is_active' => true],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['name' => $module['name']],
                $module
            );
        }
    }
}