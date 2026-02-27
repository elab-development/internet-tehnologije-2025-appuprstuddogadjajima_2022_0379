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
//Category routes

Route::resource('/categories', CategoryController::class)->except(['create', 'edit']);
/*Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);*/

//User routes
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::put('/users/{id}', [UserController::class, 'update']);

//Event routes
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::post('/events', [EventController::class, 'store']);
Route::delete('/events/{id}', [EventController::class, 'destroy']);
Route::put('/events/{id}', [EventController::class, 'update']);

//EventParticipation routes
Route::get('/event-participations', [EventParticipationController::class, 'index']);
Route::get('/event-participations/{id}', [EventParticipationController::class, 'show']);
Route::post('/event-participations', [EventParticipationController::class, 'store']);
Route::delete('/event-participations/{id}', [EventParticipationController::class, 'destroy']);
Route::put('/event-participations/{id}', [EventParticipationController::class, 'update']);

//Notification routes
Route::get('/notifications', [NotificationController::class, 'index']);
Route::get('/notifications/{id}', [NotificationController::class, 'show']);
Route::post('/notifications', [NotificationController::class, 'store']);
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
Route::put('/notifications/{id}', [NotificationController::class, 'update']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::get('/email/verify/{id}', function (Request $request, $id) {
    if (!$request->hasValidSignature()) {
        return response()->json(['message' => 'Nevažeći verifikacioni link.'], 401);
    }

    $user = App\Models\User::findOrFail($id);
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email adresa je već verifikovana.'], 400);
    }

    $user->markEmailAsVerified();
    return response()->json(['message' => 'Email adresa je uspešno verifikovana.']);
})->name('verification.verify');


Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);