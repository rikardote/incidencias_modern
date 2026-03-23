<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePortalController extends Controller
{
    /**
     * Show the employee login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }
        return view('employee_portal.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'num_empleado' => ['required'],
            'password' => ['required'],
        ]);

        // Attempt to login using the custom employee guard
        if (Auth::guard('employee')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('employee.dashboard'));
        }

        return back()->withErrors([
            'num_empleado' => 'Las credenciales proporcionadas no son correctas.',
        ])->onlyInput('num_empleado');
    }

    /**
     * Show the employee dashboard.
     */
    public function dashboard()
    {
        $empleado = Auth::guard('employee')->user();
        
        // Pass the employee to the view
        return view('employee_portal.dashboard', compact('empleado'));
    }

    /**
     * Store the Firebase Cloud Messaging Token.
     */
    public function storeFcmToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $empleado = Auth::guard('employee')->user();
        
        $empleado->fcm_token = $request->token;
        $empleado->save();

        return response()->json(['message' => 'Token saved successfully']);
    }

    /**
     * Log the employee out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }
}
