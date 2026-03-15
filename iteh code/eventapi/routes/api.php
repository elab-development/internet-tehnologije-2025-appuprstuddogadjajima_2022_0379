<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventParticipationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;

use App\Models\User;


 
//AUTH (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);


//EVENTS (public read)
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);


//CATEGORIES (public read)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);


 
//VERIFIED EMAIL
Route::get('/email/verify/{id}', function (Request $request, $id) {
    $user = User::findOrFail($id);

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email adresa je već verifikovana.'], 400);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email adresa je uspešno verifikovana.']);
})->middleware('signed')->name('verification.verify');




 
//AUTH (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    
     
//NOTIFICATIONS (ulogovan korisnik)
Route::get('/notifications', [NotificationController::class, 'index']);
Route::get('/notifications/{id}', [NotificationController::class, 'show']);
Route::post('/notifications', [NotificationController::class, 'store']);
Route::put('/notifications/{id}', [NotificationController::class, 'update']);
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);


     
//EVENT PARTICIPATIONS (ulogovan korisnik)
Route::get('/event-participations', [EventParticipationController::class, 'index']);
Route::get('/event-participations/{id}', [EventParticipationController::class, 'show']);
Route::post('/event-participations', [EventParticipationController::class, 'store']);
Route::put('/event-participations/{id}', [EventParticipationController::class, 'update']);
Route::delete('/event-participations/{id}', [EventParticipationController::class, 'destroy']);


//EVENTS (write) - ORGANIZATOR + ADMIN
Route::middleware('role:ORGANIZATOR,ADMIN')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);});

    
     
//ADMIN ONLY
Route::middleware('role:ADMIN')->group(function () {// Users CRUD
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Categories write
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});