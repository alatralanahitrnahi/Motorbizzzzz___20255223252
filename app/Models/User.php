<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'email_verified_at',
        'notification_preferences',
        'business_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'notification_preferences' => 'array',
    ];

    /**
     * Role display name (for UI)
     */
    public function getRoleDisplayName()
    {
        $roleDisplayNames = [
            'admin' => 'Administrator',
            'purchase_team' => 'Purchase Team',
            'inventory_manager' => 'Inventory Manager',
            'user' => 'User',
        ];

        return $roleDisplayNames[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * Active status display
     */
    public function getStatusDisplayAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Main role checker. Supports string or array input.
     */
    public function hasRole($roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    /**
     * Individual role shortcut helpers
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPurchaseTeam()
    {
        return $this->role === 'purchase_team';
    }

    public function isInventoryManager()
    {
        return $this->role === 'inventory_manager';
    }

  
    /**
     * Is active checker
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for role filtering
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Notification preferences helper
     */
    public function getNotificationPreferences()
    {
        return [
            'email' => $this->notification_preferences['email'] ?? true,
            'sms' => $this->notification_preferences['sms'] ?? false,
            'dashboard' => $this->notification_preferences['dashboard'] ?? true,
        ];
    }

    public function updateNotificationPreferences(array $preferences)
    {
        $this->update([
            'notification_preferences' => array_merge(
                $this->notification_preferences ?? [],
                $preferences
            )
        ]);
    }

    /**
     * Dashboard notifications shortcut
     */
    public function dashboardNotifications()
    {
        return $this->notifications()->where('type', 'dashboard');
    }

    /**
     * Relationship: Purchase Orders created by this user
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    /**
     * Relationship: Business this user belongs to
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
  
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_users')
                    ->withPivot('role')
                    ->withTimestamps();
    }
     public function permissions()
    {
        return $this->hasMany(Permission::class, 'user_id', 'id');
    }

    // Alternative method to get permissions for a specific module
    public function hasPermissionFor($moduleName, $permissionType)
    {
        return $this->permissions()
            ->whereHas('module', function ($query) use ($moduleName) {
                $query->where('name', $moduleName)
                      ->where('is_active', 1);
            })
            ->where($permissionType, true)
            ->exists();
    }



    public function getAccessibleModules()
    {
        return $this->modules()->wherePivot('can_view', true)->get();
    }
  
    /**
     * ✅ FIXED: Get modules for sidebar navigation
     */

/**
 * Get sidebar modules for the user based on their permissions
 * This method provides the modules that should be displayed in the sidebar
 */
public function getSidebarModules()
{
    try {
        // Admin users have access to all active modules
        if ($this->isAdmin()) {
            return Module::where('is_active', true)->orderBy('name')->get();
        }

        // For non-admin users, get only modules they have permissions for
        $moduleIds = $this->permissions()
            ->where('can_view', true)
            ->pluck('module_id')
            ->toArray();

        if (empty($moduleIds)) {
            // If no permissions found, return empty collection
            // The controller will handle default permissions
            return collect();
        }

        return Module::whereIn('id', $moduleIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    } catch (\Exception $e) {
        Log::error("Error getting sidebar modules for user {$this->email}: " . $e->getMessage());
        return collect();
    }
}

/**
 * Check if user has permission to view a specific module
 */
public function canViewModule($moduleName)
{
    if ($this->isAdmin()) {
        return true;
    }

    return $this->permissions()
        ->whereHas('module', function ($query) use ($moduleName) {
            $query->where('name', $moduleName);
        })
        ->where('can_view', true)
        ->exists();
}

/**
 * Check if user has permission to edit a specific module
 */
public function canEditModule($moduleName)
{
    if ($this->isAdmin()) {
        return true;
    }

    return $this->permissions()
        ->whereHas('module', function ($query) use ($moduleName) {
            $query->where('name', $moduleName);
        })
        ->where('can_edit', true)
        ->exists();
}

    /**
     * ✅ ADDITIONAL: Helper method to check if user has any permission for a module
     */
 public function hasModulePermission($moduleId, $action = 'view'): bool
{
    $column = 'can_' . $action;

    return $this->permissions()
        ->where('module_id', $moduleId)
        ->where($column, true)
        ->exists();
}

    /**
     * ✅ ADDITIONAL: Get user's specific permissions for a module
     */
    public function getModulePermissions($moduleId)
    {
        if ($this->role === 'admin') {
            return [
                'can_view' => true,
                'can_create' => true,
                'can_edit' => true,
                'can_delete' => true,
            ];
        }

        $permission = $this->permissions()->where('module_id', $moduleId)->first();

        if (!$permission) {
            return [
                'can_view' => false,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false,
            ];
        }

        return [
            'can_view' => (bool) $permission->can_view,
            'can_create' => (bool) $permission->can_create,
            'can_edit' => (bool) $permission->can_edit,
            'can_delete' => (bool) $permission->can_delete,
        ];
    }
}