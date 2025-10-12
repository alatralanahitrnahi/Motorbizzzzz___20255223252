<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_slug' => ['required', 'string', 'max:50', 'unique:businesses,slug', 'regex:/^[a-z0-9-]+$/'],
            'business_phone' => ['required', 'string', 'max:20'],
            'business_address' => ['required', 'string', 'max:500'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create business
        $business = Business::create([
            'name' => $request->business_name,
            'slug' => Str::lower($request->business_slug),
            'phone' => $request->business_phone,
            'address' => $request->business_address,
            'email' => $request->email,
            'is_active' => true,
            'subscription_plan' => 'free',
        ]);

        // Create owner user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_active' => true,
            'business_id' => $business->id,
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome to Monitorbizz! Your workshop is ready.');
    }
}