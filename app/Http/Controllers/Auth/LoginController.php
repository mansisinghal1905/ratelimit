<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $ipAddress = $request->ip();

        // Check if there is an active session for this IP
        $activeSession = DB::table('sessions')
                            ->where('ip_address', $ipAddress)
                            ->where('is_active', 1)
                            ->first();

        if ($activeSession) {
            // If an active session exists, return a view with a prompt
            return view('session.exists', ['ip' => $ipAddress, 'session_id' => $activeSession->id]);
        }

        // Authenticate the user
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            // Create a new session
            $sessionId = DB::table('sessions')->insertGetId([
                'ip_address' => $ipAddress,
                'user_id' => Auth::id(),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            

            // Store the session ID in the user's session
            Session::put('session_id', $sessionId);

            return redirect()->intended('/dashboard');
        }else{
            $user = \App\Models\User::create([
                'name' => "mansi",
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        // Deactivate the current session
        $sessionId = Session::get('session_id');
        DB::table('sessions')
            ->where('id', $sessionId)
            ->update(['is_active' => 0]);

        Auth::logout();

        return redirect('/login');
    }
}
