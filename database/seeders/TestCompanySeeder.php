<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Material;
use App\Models\Product;
use App\Models\Machine;
use App\Models\InventoryLocation;
use App\Models\Bom;
use App\Models\BomItem;

class TestCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Find or create test business
        $business = Business::firstOrCreate([
            'slug' => 'abc-steel-fabrication'
        ], [
            'name' => 'ABC Steel Fabrication',
            'email' => 'info@abcsteel.com',
            'phone' => '9876543210',
            'address' => 'Industrial Area, Pune, Maharashtra',
            'is_active' => true,
            'subscription_plan' => 'premium'
        ]);

        // Create admin user if doesn't exist
        $user = User::firstOrCreate([
            'email' => 'rajesh@abcsteel.com'
        ], [
            'business_id' => $business->id,
            'name' => 'Rajesh Kumar',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true
        ]);

        // Create inventory locations
        $warehouse = InventoryLocation::firstOrCreate([
            'code' => 'WH-001',
            'business_id' => $business->id
        ], [
            'name' => 'Main Warehouse',
            'location_type' => 'warehouse',
            'capacity' => 50000,
            'address' => 'Building A, Ground Floor',
            'is_active' => true
        ]);

        $shopFloor = InventoryLocation::firstOrCreate([
            'code' => 'SF-001',
            'business_id' => $business->id
        ], [
            'name' => 'Shop Floor - Bay 1',
            'location_type' => 'shop_floor',
            'capacity' => 10000,
            'is_active' => true
        ]);

        // Create raw materials
        $steel = Material::firstOrCreate([
            'code' => 'MS-2MM-001',
            'business_id' => $business->id
        ], [
            'name' => 'Mild Steel Sheet 2mm',
            'unit' => 'kg',
            'unit_price' => 45.50,
            'gst_rate' => 18.00,
            'category' => 'Metal',
            'material_type' => 'raw_material',
            'current_stock' => 2500,
            'reorder_level' => 500,
            'location_id' => $warehouse->id,
            'is_active' => true
        ]);

        $paint = Material::firstOrCreate([
            'code' => 'PE-RED-001',
            'business_id' => $business->id
        ], [
            'name' => 'Industrial Enamel Paint - Red',
            'unit' => 'liter',
            'unit_price' => 180.00,
            'gst_rate' => 18.00,
            'category' => 'Consumable',
            'material_type' => 'consumable',
            'current_stock' => 200,
            'reorder_level' => 50,
            'location_id' => $warehouse->id,
            'is_active' => true
        ]);

        $bolts = Material::firstOrCreate([
            'code' => 'BOLT-M8-001',
            'business_id' => $business->id
        ], [
            'name' => 'M8 Hex Bolts - Grade 8.8',
            'unit' => 'pieces',
            'unit_price' => 1.75,
            'gst_rate' => 18.00,
            'category' => 'Hardware',
            'material_type' => 'component',
            'current_stock' => 5000,
            'reorder_level' => 1000,
            'location_id' => $warehouse->id,
            'is_active' => true
        ]);

        $handles = Material::firstOrCreate([
            'code' => 'HANDLE-AL-001',
            'business_id' => $business->id
        ], [
            'name' => 'Aluminum Cabinet Handle',
            'unit' => 'pieces',
            'unit_price' => 45.00,
            'gst_rate' => 18.00,
            'category' => 'Component',
            'material_type' => 'component',
            'current_stock' => 500,
            'reorder_level' => 100,
            'location_id' => $warehouse->id,
            'is_active' => true
        ]);

        // Create products
        $cabinet = Product::firstOrCreate([
            'product_code' => 'CAB-4D-001',
            'business_id' => $business->id
        ], [
            'name' => 'Industrial Steel Cabinet - 4 Door',
            'unit' => 'pieces',
            'selling_price' => 4500.00,
            'cost_price' => 2800.00,
            'category' => 'Furniture',
            'product_type' => 'finished_good',
            'current_stock' => 50,
            'reorder_level' => 20,
            'manufacturing_time' => 180, // minutes
            'is_manufactured' => true,
            'is_saleable' => true,
            'location_id' => $warehouse->id
        ]);

        $drawer = Product::firstOrCreate([
            'product_code' => 'DRAW-3T-001',
            'business_id' => $business->id
        ], [
            'name' => 'Steel Drawer Unit - 3 Tier',
            'unit' => 'pieces',
            'selling_price' => 1200.00,
            'cost_price' => 750.00,
            'category' => 'Furniture',
            'product_type' => 'semi_finished',
            'current_stock' => 100,
            'reorder_level' => 50,
            'manufacturing_time' => 90, // minutes
            'is_manufactured' => true,
            'is_saleable' => true,
            'location_id' => $warehouse->id
        ]);

        // Create BOM for cabinet if doesn't exist
        $cabinetBom = Bom::firstOrCreate([
            'product_id' => $cabinet->id,
            'version' => '1.0'
        ], [
            'business_id' => $business->id,
            'quantity' => 1,
            'is_active' => true
        ]);

        // Clear existing BOM items and recreate
        if ($cabinetBom->wasRecentlyCreated) {
            BomItem::create([
                'bom_id' => $cabinetBom->id,
                'material_id' => $steel->id,
                'quantity_required' => 25, // kg per cabinet
                'unit' => 'kg',
                'wastage_percent' => 5
            ]);

            BomItem::create([
                'bom_id' => $cabinetBom->id,
                'material_id' => $paint->id,
                'quantity_required' => 0.75, // liter per cabinet
                'unit' => 'liter',
                'wastage_percent' => 8
            ]);

            BomItem::create([
                'bom_id' => $cabinetBom->id,
                'material_id' => $bolts->id,
                'quantity_required' => 32, // pieces per cabinet
                'unit' => 'pieces',
                'wastage_percent' => 2
            ]);

            BomItem::create([
                'bom_id' => $cabinetBom->id,
                'material_id' => $handles->id,
                'quantity_required' => 4, // pieces per cabinet
                'unit' => 'pieces',
                'wastage_percent' => 1
            ]);
        }

        // Create BOM for drawer (semi-finished) if doesn't exist
        $drawerBom = Bom::firstOrCreate([
            'product_id' => $drawer->id,
            'version' => '1.0'
        ], [
            'business_id' => $business->id,
            'quantity' => 1,
            'is_active' => true
        ]);

        // Clear existing BOM items and recreate
        if ($drawerBom->wasRecentlyCreated) {
            BomItem::create([
                'bom_id' => $drawerBom->id,
                'material_id' => $steel->id,
                'quantity_required' => 8, // kg per drawer
                'unit' => 'kg',
                'wastage_percent' => 4
            ]);

            BomItem::create([
                'bom_id' => $drawerBom->id,
                'material_id' => $bolts->id,
                'quantity_required' => 12, // pieces per drawer
                'unit' => 'pieces',
                'wastage_percent' => 2
            ]);
        }

        // Create machines
        $cncMachine = Machine::firstOrCreate([
            'code' => 'CNC-2000-001',
            'business_id' => $business->id
        ], [
            'name' => 'CNC Plasma Cutter - 2000W',
            'type' => 'cnc',
            'status' => 'available',
            'location' => 'Shop Floor - Bay 1'
        ]);

        $weldingMachine = Machine::firstOrCreate([
            'code' => 'MIG-250-001',
            'business_id' => $business->id
        ], [
            'name' => 'MIG Welding Machine - 250A',
            'type' => 'welding',
            'status' => 'available',
            'location' => 'Shop Floor - Bay 2'
        ]);

        $paintingBooth = Machine::firstOrCreate([
            'code' => 'SPRAY-001',
            'business_id' => $business->id
        ], [
            'name' => 'Spray Painting Booth',
            'type' => 'other',
            'status' => 'available',
            'location' => 'Painting Section'
        ]);

        // Create customers
        $corporateCustomer = Customer::firstOrCreate([
            'customer_code' => 'CUST-CORP-001',
            'business_id' => $business->id
        ], [
            'name' => 'XYZ Corporation',
            'company_name' => 'XYZ Corporation Pvt. Ltd.',
            'email' => 'procurement@xyzcorp.com',
            'phone' => '9876543211',
            'gstin' => '27AABCCDDEEFFG',
            'billing_address' => 'Business Park, Mumbai, Maharashtra',
            'shipping_address' => 'Factory Address, Mumbai, Maharashtra',
            'customer_type' => 'wholesale',
            'credit_limit' => 200000.00,
            'payment_terms' => 30,
            'status' => 'active'
        ]);

        $retailCustomer = Customer::firstOrCreate([
            'customer_code' => 'CUST-RTL-001',
            'business_id' => $business->id
        ], [
            'name' => 'Ramesh Iyer',
            'email' => 'ramesh.iyer@gmail.com',
            'phone' => '9876543212',
            'customer_type' => 'retail',
            'credit_limit' => 50000.00,
            'payment_terms' => 7,
            'status' => 'active'
        ]);

        // Create vendors
        $steelVendor = Vendor::firstOrCreate([
            'name' => 'Premium Steel Suppliers',
            'business_id' => $business->id
        ], [
            'email' => 'orders@premiumsteel.com',
            'phone' => '9876543213',
            'address' => 'Steel Market, Bhiwandi, Thane',
            'gstin' => '27AABBCCDDEEFFH'
        ]);

        $hardwareVendor = Vendor::firstOrCreate([
            'name' => 'Hardware World',
            'business_id' => $business->id
        ], [
            'email' => 'sales@hardwareworld.com',
            'phone' => '9876543214',
            'address' => 'Hardware Market, Pune',
            'gstin' => '27AABBCCDDEEFFI'
        ]);

        echo "✅ Test company 'ABC Steel Fabrication' created successfully!\n";
        echo "Business ID: {$business->id}\n";
        echo "Admin User: rajesh@abcsteel.com / password123\n";
        echo "Materials: Steel (2500kg), Paint (200L), Bolts (5000pcs), Handles (500pcs)\n";
        echo "Products: Cabinets (50 units), Drawers (100 units)\n";
        echo "Machines: CNC, Welding, Painting Booth\n";
        echo "Customers: XYZ Corp (wholesale), Ramesh Iyer (retail)\n";
        echo "Vendors: Premium Steel, Hardware World\n";
    }
}
