<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\LoginController;

Route::any('/close-session', function (Request $request) {
    $ip = $request->input('ip');
    $sessionId = $request->input('session_id');

    // Deactivate the old session
    DB::table('sessions')
        ->where('id', $sessionId)
        ->update(['is_active' => 0]);

    return response()->json(['status' => 'success']);
});

Route::post('test',[LoginController::class, 'login'])->name('test');
Route::post('clogout',[LoginController::class, 'logout'])->name('clogout');


Route::group(['middleware' => ['rate.limit.ip']], function () {
    // Add routes that require rate limiting here

    Route::get('/das', function () {
        return "hello";
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
require __DIR__.'/auth.php';
