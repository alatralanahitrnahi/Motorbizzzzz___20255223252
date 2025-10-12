<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vendor;
use App\Models\InventoryBatch;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;

use App\Models\QualityAnalysis;
use App\Models\BarcodeLog;
use App\Models\StockMovement;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Display the dashboard */
    public function index()
    {
        try {
            $user = Auth::user();
            $cacheKey = "dashboard_stats_{$user->id}";
            
            // If ?refresh=1 is present, clear the cache
            if (request()->has('refresh')) {
                Cache::forget($cacheKey);
                Log::info("Dashboard stats cache cleared for user: {$user->email}");
            }
            
            // Cache dashboard stats for 5 minutes
          $stats = Cache::remember($cacheKey, 300, function () use ($user) {
    return $this->getDashboardStats($user);
});

            
            $navigationItems = $this->getNavigationItems($user);
            
            // ✅ Get Sidebar Modules - FIXED: Uncommented and properly implemented
            $sidebarModules = $user->getSidebarModules();
            
            // Optional: log stats array for debugging
            Log::info("Dashboard stats for user {$user->email}:", $stats);
            Log::info("Sidebar modules for user {$user->email}:", $sidebarModules->toArray());
            
            // Fetch pending material requests (Admin or Inventory Manager only)
            $pendingMaterials = collect();
            
            return view('dashboard', compact(
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard. Please try again.');
        }
    }

    /** Get navigation items based on user permissions - SIMPLIFIED */
    private function getNavigationItems($user): array
    {
        // Navigation is now handled by Gates in the view
        // This method is kept for backward compatibility
        return [
            'dashboard' => [
                'title' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'dashboard',
                'active' => true,
            ],
        ];
    }
      
    private function getFirstWarehouseId($user)
    {
        // Get first warehouse ID with better error handling
        try {
            $warehouse = $user->warehouses()->first();
            if ($warehouse) {
                return $warehouse->id;
            }
            
            // Fallback to first available warehouse
            $firstWarehouse = Warehouse::first();
            return $firstWarehouse ? $firstWarehouse->id : 1;
        } catch (\Exception $e) {
            Log::error('Error getting first warehouse ID: ' . $e->getMessage());
            return 1; // Ultimate fallback
        }
    }

    /** Helper methods for permission checking - SIMPLIFIED */
    private function hasViewPermission($permission): bool
    {
        return (bool) ($permission->can_view ?? false);
    }

    private function hasEditPermission($permission): bool
    {
        return (bool) ($permission->can_edit ?? false);
    }

    /** Generate dashboard statistics based on user role */
    private function getDashboardStats($user)
    {
        try {
            $businessId = $user->business_id;
            
            // Get basic stats for multi-tenant context
            $stats = [
                'materials' => \App\Models\Material::where('business_id', $businessId)->count(),
                'vendors' => \App\Models\Vendor::where('business_id', $businessId)->count(),
                'purchase_orders' => \App\Models\PurchaseOrder::where('business_id', $businessId)->count(),
                'machines' => \App\Models\Machine::where('business_id', $businessId)->count(),
                'work_orders' => \App\Models\WorkOrder::where('business_id', $businessId)->count(),
                'invoices' => \App\Models\Invoice::where('business_id', $businessId)->count(),
            ];

            $stats['user_role'] = $user->getRoleDisplayName();
            $stats['last_login'] = $user->last_login_at?->diffForHumans() ?? 'First time login';

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error generating dashboard stats: ' . $e->getMessage());
            return $this->getEmptyStats($user);
        }
    }
    
    private function getDefaultUserStats($user): array
    {
        $businessId = $user->business_id;
        
        return [
            'materials' => \App\Models\Material::where('business_id', $businessId)->count(),
            'vendors' => \App\Models\Vendor::where('business_id', $businessId)->count(),
            'purchase_orders' => \App\Models\PurchaseOrder::where('business_id', $businessId)->count(),
            'machines' => \App\Models\Machine::where('business_id', $businessId)->count(),
        ];
    }



/**
 * ✅ FIXED: Admin statistics with improved queries and debugging
 */
private function getAdminStats(): array
{
    try {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'purchase_team_users' => User::where('role', 'purchase_team')->count(),
            'inventory_managers' => User::where('role', 'inventory_manager')->count(),

            // ✅ FIXED: Inventory stats with debugging
            'total_inventory_items' => InventoryBatch::count(),
            'low_stock_items' => $this->getLowStockCount(),
            'out_of_stock' => InventoryBatch::where('current_quantity', '<=', 0)->count(),

            'total_vendors' => Vendor::count(),
            'total_purchase_orders' => PurchaseOrder::count(),
            'pending_orders' => PurchaseOrder::where('status', 'pending')->count(),
            'total_warehouses' => $this->getTotalWarehouses(),
            
            // ✅ FIXED: Quality checks with better error handling
            'pending_quality_checks' => $this->getPendingQualityChecks(),
          'approved_quality_checks' => $this->getApprovedQualityChecks(),

            // ✅ FIXED: Recent logins - users who logged in within last 7 days
            'recent_logins' => $this->getRecentLogins(),

            'monthly_transactions' => $this->getMonthlyTransactions(),
        ];

        // Debug logging
        Log::info('Admin Dashboard Stats Generated:', $stats);
        
        return $stats;
    } catch (\Exception $e) {
        Log::error('Error in getAdminStats: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return $this->getEmptyAdminStats();
    }
}

  /**
 * ✅ Full stats method for Purchase Team Dashboard
 */
private function getPurchaseTeamStats(): array
{
    try {
        return [
            'total_orders'    => PurchaseOrder::count(),
            'pending_orders'  => PurchaseOrder::where('status', 'pending')->count(),
            'total_vendors'   => Vendor::count(),
            'budget_utilized' => $this->getBudgetUtilized(), // Implemented below
        ];
    } catch (\Exception $e) {
        Log::error('Error in getPurchaseTeamStats (Minimal): ' . $e->getMessage());
        return [
            'total_orders'    => 0,
            'pending_orders'  => 0,
            'total_vendors'   => 0,
            'budget_utilized' => 0,
        ];
    }
}
private function getBudgetUtilized(): float
{
    return PurchaseOrder::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');
}

  
/**
 * ✅ FIXED: Inventory Manager statistics with debugging
 */
private function getInventoryManagerStats(): array
{
    try {
        $stats = [
            'total_items' => InventoryBatch::count(),
            'low_stock_items' => $this->getLowStockCount(),
            'out_of_stock' => InventoryBatch::where('current_quantity', '<=', 0)->count(),
            'items_added_today' => InventoryBatch::whereDate('created_at', Carbon::today())->count(),
            'items_updated_today' => InventoryBatch::whereDate('updated_at', Carbon::today())
                ->where('updated_at', '>', DB::raw('created_at'))
                ->count(),
            'total_warehouses' => Warehouse::count(),
            'pending_material_requests' => 0,
        ];

        // Debug logging
        Log::info('Inventory Manager Dashboard Stats Generated:', $stats);
        
        return $stats;
    } catch (\Exception $e) {
        Log::error('Error in getInventoryManagerStats: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return $this->getEmptyInventoryStats();
    }
}

/**
 * ✅ FIXED: Low stock count with multiple approaches and debugging
 */
private function getLowStockCount(): int
{
    try {
        // Method 1: Simple query
        $lowStockCount = InventoryBatch::where('current_quantity', '<=', 10)
            ->where('current_quantity', '>', 0)
            ->count();
        
        Log::info("Low stock count (Method 1): $lowStockCount");

        // Method 2: Alternative query if first one returns 0
        if ($lowStockCount === 0) {
            $lowStockCount = InventoryBatch::whereBetween('current_quantity', [1, 10])->count();
            Log::info("Low stock count (Method 2): $lowStockCount");
        }

        // Method 3: Debug - check if there's any data at all
        $totalCount = InventoryBatch::count();
        $zeroCount = InventoryBatch::where('current_quantity', 0)->count();
        $negativeCount = InventoryBatch::where('current_quantity', '<', 0)->count();
        $positiveCount = InventoryBatch::where('current_quantity', '>', 0)->count();
        
        Log::info("Inventory Debug - Total: $totalCount, Zero: $zeroCount, Negative: $negativeCount, Positive: $positiveCount");

        // Method 4: Check column names (in case it's different)
        $sampleRecord = InventoryBatch::first();
        if ($sampleRecord) {
            Log::info("Sample inventory record attributes:", $sampleRecord->getAttributes());
        }

        return $lowStockCount;
        
    } catch (\Exception $e) {
        Log::error('Error getting low stock count: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return 0;
    }
}

/**
 * ✅ FIXED: Recent logins with multiple approaches and debugging
 */
private function getRecentLogins(): int
{
    try {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        
        // Method 1: Check for last_login_at column
        $recentLogins = User::where('last_login_at', '>=', $sevenDaysAgo)
            ->whereNotNull('last_login_at')
            ->count();
        
        Log::info("Recent logins (Method 1): $recentLogins");
        Log::info("Date filter: $sevenDaysAgo");

        // Method 2: Alternative - use updated_at if last_login_at doesn't exist or is null
        if ($recentLogins === 0) {
            $recentLogins = User::where('updated_at', '>=', $sevenDaysAgo)->count();
            Log::info("Recent logins (Method 2 - updated_at): $recentLogins");
        }

        // Debug: Check if last_login_at column exists and has data
        $usersWithLastLogin = User::whereNotNull('last_login_at')->count();
        $totalUsers = User::count();
        Log::info("Debug - Total users: $totalUsers, Users with last_login_at: $usersWithLastLogin");

        // Sample a user to check the data structure
        $sampleUser = User::first();
        if ($sampleUser) {
            Log::info("Sample user last_login_at: " . $sampleUser->last_login_at);
            Log::info("Sample user attributes:", array_keys($sampleUser->getAttributes()));
        }

        return $recentLogins;
        
    } catch (\Exception $e) {
        Log::error('Error getting recent logins: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return 0;
    }
}

private function getPendingQualityChecks(): int
{
    try {
        $count = QualityAnalysis::where('quality_status', 'pending')->count();
        Log::info("Pending Quality Check Count: $count");
        return $count;
    } catch (\Exception $e) {
        Log::error('Error fetching pending quality checks: ' . $e->getMessage());
        return 0;
    }
}

private function getApprovedQualityChecks(): int
{
    try {
        $count = QualityAnalysis::where('quality_status', 'approved')->count();
        Log::info("Approved Quality Check Count: $count");
        return $count;
    } catch (\Exception $e) {
        Log::error('Error fetching approved quality checks: ' . $e->getMessage());
        return 0;
    }
}


/**
 * Debug method to check database structure and data
 */
public function debugDashboardStats()
{
    if (!Auth::user()->isAdmin()) {
        abort(403, 'Unauthorized');
    }

    $debug = [];
    
    try {
        // Check InventoryBatch table
        $debug['inventory'] = [
            'total_count' => InventoryBatch::count(),
            'with_quantity' => InventoryBatch::whereNotNull('current_quantity')->count(),
            'sample_quantities' => InventoryBatch::take(5)->pluck('current_quantity')->toArray(),
            'quantity_stats' => [
                'min' => InventoryBatch::min('current_quantity'),
                'max' => InventoryBatch::max('current_quantity'),
                'avg' => round(InventoryBatch::avg('current_quantity') ?? 0, 2),
            ]
        ];

        // Check Users table
        $debug['users'] = [
            'total_count' => User::count(),
            'with_last_login' => User::whereNotNull('last_login_at')->count(),
            'recent_7_days' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count(),
            'sample_last_logins' => User::whereNotNull('last_login_at')->take(3)->pluck('last_login_at')->toArray(),
        ];

        // Check QualityAnalysis table
        $debug['quality'] = [
            'total_count' => QualityAnalysis::count(),
            'status_distribution' => QualityAnalysis::groupBy('quality_status')->selectRaw('quality_status, count(*) as count')->get()->pluck('count', 'quality_status')->toArray(),
            'columns' => QualityAnalysis::first()?->getAttributes() ? array_keys(QualityAnalysis::first()->getAttributes()) : [],
        ];
      // Check PurchaseOrder table
$debug['purchase_orders'] = [
    'total' => PurchaseOrder::count(),
    'status_counts' => PurchaseOrder::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray(),
    'recent_orders' => PurchaseOrder::latest()->take(3)->get(['id', 'status', 'total_amount', 'created_at'])->toArray(),
    'in_current_month' => PurchaseOrder::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count(),
    'budget_utilized' => $this->getBudgetUtilized(), // ✅ Add your actual logic
];

// Check Vendor table
$debug['vendors'] = [
    'total' => Vendor::count(),
    'sample_names' => Vendor::take(3)->pluck('name')->toArray(),
];


    } catch (\Exception $e) {
        $debug['error'] = $e->getMessage();
    }

    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
}


  

    /** Empty stats fallback methods */
    private function getEmptyStats($user): array
    {
        return [
            'user_role' => $user->getRoleDisplayName(),
            'last_login' => $user->last_login_at?->diffForHumans() ?? 'First time login',
            'error' => 'Unable to load statistics'
        ];
    }

    private function getEmptyAdminStats(): array
    {
        return [
            'total_users' => 0,
            'active_users' => 0,
            'total_vendors' => 0,
            'total_inventory_items' => 0,
            'low_stock_items' => 0,
            'out_of_stock' => 0,
            'total_purchase_orders' => 0,
            'pending_orders' => 0,
            'total_warehouses' => 0,
            'pending_quality_checks' => 0,
            'recent_logins' => 0,
            'monthly_transactions' => 0,
        ];
    }

  

    private function getEmptyInventoryStats(): array
    {
        return [
            'total_items' => 0,
            'low_stock_items' => 0,
            'out_of_stock' => 0,
            'total_warehouses' => 0,
            'pending_material_requests' => 0,
        ];
    }

    /** Show users management page - Admin only */
    public function showUsers(Request $request)
    {
        $this->authorizeAdmin();

        try {
            $query = User::query();

            // Apply search filters
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            $users = $query->latest()->paginate(15);

            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', 1)->count(),
                'inactive_users' => User::where('is_active', 0)->count(),
                'total_vendors' => Vendor::count(),
                'total_items' => InventoryItem::count(),
                'total_orders' => PurchaseOrder::count(),
            ];

            return view('dashboard.users', compact('users', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in showUsers: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Unable to load users page.');
        }
    }

    /** Store a new user - Admin only */
    public function storeUser(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,purchase_team,inventory_manager,user',
            'is_active' => 'required|boolean',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $newUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
                'phone' => $validated['phone'],
                'email_verified_at' => now(),
            ]);

            // Assign default permissions based on role
            $this->assignDefaultPermissions($newUser);

            DB::commit();

            Log::info('User created successfully', ['user_id' => $newUser->id, 'created_by' => Auth::id()]);

            return response()->json([
                'success' => true, 
                'message' => 'User created successfully!', 
                'user' => $newUser->only(['id', 'name', 'email', 'role', 'is_active'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign default permissions to a new user based on their role
     */
    private function assignDefaultPermissions(User $user)
    {
        try {
            // Don't assign permissions to admin users - they have access to everything
            if ($user->role === 'admin') {
                return;
            }

            // Get modules based on role
            $modulePermissions = $this->getDefaultModulePermissions($user->role);
            
            foreach ($modulePermissions as $moduleName => $permissions) {
                // Find the module by name
                $module = Module::where('name', $moduleName)->first();
                
                if ($module) {
                    // Check if permission already exists
                    $existingPermission = $user->permissions()->where('module_id', $module->id)->first();
                    
                    if (!$existingPermission) {
                        // Create permission record
                        $user->permissions()->create([
                            'module_id' => $module->id,
                            'can_view' => $permissions['can_view'],
                            'can_edit' => $permissions['can_edit'],
                        ]);
                        
                        Log::info("Assigned permission for module {$moduleName} to user {$user->email}");
                    } else {
                        Log::info("Permission for module {$moduleName} already exists for user {$user->email}");
                    }
                } else {
                    Log::warning("Module {$moduleName} not found when assigning permissions to user {$user->email}");
                }
            }
            
            Log::info("Default permissions assigned to user {$user->email}");
        } catch (\Exception $e) {
            Log::error("Error assigning default permissions to user {$user->email}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fix permissions for existing users who don't have any - Admin only
     */
    public function fixUserPermissions(Request $request)
    {
        $this->authorizeAdmin();
        
        try {
            $usersWithoutPermissions = User::whereDoesntHave('permissions')
                ->where('role', '!=', 'admin')
                ->get();
            
            $fixedCount = 0;
            
            foreach ($usersWithoutPermissions as $user) {
                $this->assignDefaultPermissions($user);
                $fixedCount++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Fixed permissions for {$fixedCount} users",
                'users_fixed' => $fixedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error fixing user permissions: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fix user permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get default module permissions for each role
     */
    private function getDefaultModulePermissions($role): array
    {
        switch ($role) {
            case 'purchase_team':
                return [
                    'vendor_management' => ['can_view' => true, 'can_edit' => true],
                    'purchase_orders' => ['can_view' => true, 'can_edit' => true],
                    'materials' => ['can_view' => true, 'can_edit' => false],
                ];
                
            case 'inventory_manager':
                return [
                    'inventory_control' => ['can_view' => true, 'can_edit' => true],
                    'warehouse_management' => ['can_view' => true, 'can_edit' => true],
                    'barcode_management' => ['can_view' => true, 'can_edit' => true],
                    'materials' => ['can_view' => true, 'can_edit' => true],
                    'quality_analysis' => ['can_view' => true, 'can_edit' => true],
                ];
                
            case 'user':
                return [
                    'materials' => ['can_view' => true, 'can_edit' => false],
                    'inventory_control' => ['can_view' => true, 'can_edit' => false],
                ];
                
            default:
                return [];
        }
    }

    /** Update user active status - Admin only */
    public function updateUserStatus(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'is_active' => 'required|boolean'
        ]);

        try {
            $user->update(['is_active' => $validated['is_active']]);

            Log::info('User status updated', [
                'user_id' => $user->id,
                'new_status' => $validated['is_active'],
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'User activation status updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user status: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update user status'
            ], 500);
        }
    }

    /** Deactivate a user - Admin only */
    public function deleteUser(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->id === $currentUser->id) {
            return response()->json([
                'error' => 'You cannot deactivate your own account'
            ], 400);
        }

        try {
            $user->update(['is_active' => 0]);
            
            Log::info('User deactivated', [
                'user_id' => $user->id,
                'deactivated_by' => $currentUser->id
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'User deactivated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deactivating user: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to deactivate user: ' . $e->getMessage()
            ], 500);
        }
    }

    /** Helper authorization for admin */
    private function authorizeAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }

    /** ✅ FIXED: Helper methods for statistics with improved error handling */
    private function getProfileCompletion($user): int 
    { 
        $completion = 50; // Base completion
        if ($user->email_verified_at) $completion += 20;
        if ($user->phone) $completion += 15;
        if ($user->last_login_at) $completion += 15;
        return min($completion, 100);
    }

    private function getTotalWarehouses(): int 
    { 
        try {
            return Warehouse::count();
        } catch (\Exception $e) {
            Log::error('Error getting total warehouses: ' . $e->getMessage());
            return 0;
        }
    }

    private function getMonthlyBudget(): int 
    { 
        // This should come from settings or budget table
        return 150000; 
    }


  

    private function getMonthlyTransactions(): int
    {
        try {
            return PurchaseOrder::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting monthly transactions: ' . $e->getMessage());
            return 0;
        }
    }
    /** Debug method for permissions troubleshooting */
    public function debugPermissions()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        
        $output = "<h3>User Information:</h3>";
        $output .= "Name: " . $user->name . "<br>";
        $output .= "Email: " . $user->email . "<br>";
        $output .= "Role: " . $user->role . "<br>";
        $output .= "Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "<br><br>";
        
        $output .= "<h3>User Permissions:</h3>";
        $permissions = $user->permissions()->with('module')->get();
        
        if ($permissions->isEmpty()) {
            $output .= "No permissions found for this user.<br>";
            $output .= "Providing default access based on role: " . $user->role . "<br>";
        } else {
            $output .= "<table border='1' style='border-collapse: collapse;'>";
            $output .= "<tr><th style='padding: 8px;'>Module</th><th style='padding: 8px;'>Can View</th><th style='padding: 8px;'>Can Edit</th></tr>";
            foreach ($permissions as $permission) {
                $output .= "<tr>";
                $output .= "<td style='padding: 8px;'>" . ($permission->module ? $permission->module->name : 'No Module') . "</td>";
                $output .= "<td style='padding: 8px;'>" . ($permission->can_view ? 'Yes' : 'No') . "</td>";
                $output .= "<td style='padding: 8px;'>" . ($permission->can_edit ? 'Yes' : 'No') . "</td>";
                $output .= "</tr>";
            }
            $output .= "</table>";
        }
        
        $output .= "<h3>Available Modules:</h3>";
        $modules = Module::where('is_active', true)->get();
        $output .= "<table border='1' style='border-collapse: collapse;'>";
        $output .= "<tr><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Name</th><th style='padding: 8px;'>Display Name</th><th style='padding: 8px;'>Is Active</th></tr>";
        foreach ($modules as $module) {
            $output .= "<tr>";
            $output .= "<td style='padding: 8px;'>" . $module->id . "</td>";
            $output .= "<td style='padding: 8px;'>" . $module->name . "</td>";
            $output .= "<td style='padding: 8px;'>" . $module->display_name . "</td>";
            $output .= "<td style='padding: 8px;'>" . ($module->is_active ? 'Yes' : 'No') . "</td>";
            $output .= "</tr>";
        }
        $output .= "</table>";

        return response($output);
    }
  
}