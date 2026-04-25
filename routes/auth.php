<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\BookingRequestController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\StandLocationController;
use App\Http\Controllers\StandLocationScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/menu-items', [MenuItemController::class, 'index']);
    Route::get('/menu-items/count', [MenuItemController::class, 'count']);

    Route::get('/stand-locations/count', [StandLocationController::class, 'count']);
    Route::get('/stand-locations', [StandLocationController::class, 'index']);

    Route::get('/contact-messages', [ContactMessageController::class, 'index']);
    Route::patch('/contact-messages/{contactMessage}/mark-read', [ContactMessageController::class, 'markRead'])->name('admin.messages.markRead');
    Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('admin.messages.destroy');

    Route::get('/booking-requests/count', [BookingRequestController::class, 'count']);
    Route::get('/booking-requests', [BookingRequestController::class, 'index']);
    Route::patch('/booking-requests/{bookingRequest}/mark-read', [BookingRequestController::class, 'markRead'])->name('admin.bookings.markRead');
    Route::patch('/booking-requests/{bookingRequest}/status', [BookingRequestController::class, 'updateStatus'])->name('admin.bookings.updateStatus');
    Route::delete('/booking-requests/{bookingRequest}', [BookingRequestController::class, 'destroy'])->name('admin.bookings.destroy');

    Route::post('/menu-items', [MenuItemController::class, 'store'])->name('admin.menu.store');
    Route::put('/menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('admin.menu.update');
    Route::delete('/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('admin.menu.destroy');
    Route::patch('/menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggle'])->name('admin.menu.toggle');

    Route::get('/menu-items', [MenuItemController::class, 'index']);
    Route::get('/menu-items/count', [MenuItemController::class, 'count']);
    Route::post('/menu-items', [MenuItemController::class, 'store'])->name('admin.menu.store');
    Route::put('/menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('admin.menu.update');
    Route::delete('/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('admin.menu.destroy');
    Route::patch('/menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggle'])->name('admin.menu.toggle');

// Menu categories
    Route::get('/menu-categories', [MenuCategoryController::class, 'index']);
    Route::post('/menu-categories', [MenuCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/menu-categories/{menuCategory}', [MenuCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/menu-categories/{menuCategory}', [MenuCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Reusable locations
    Route::get('/stand-locations', [StandLocationController::class, 'index']);
    Route::post('/stand-locations', [StandLocationController::class, 'store'])->name('admin.locations.store');
    Route::put('/stand-locations/{standLocation}', [StandLocationController::class, 'update'])->name('admin.locations.update');
    Route::delete('/stand-locations/{standLocation}', [StandLocationController::class, 'destroy'])->name('admin.locations.destroy');

// Scheduled appearances
    Route::get('/stand-location-schedules', [StandLocationScheduleController::class, 'index']);
    Route::post('/stand-location-schedules', [StandLocationScheduleController::class, 'store'])->name('admin.schedules.store');
    Route::put('/stand-location-schedules/{standLocationSchedule}', [StandLocationScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::delete('/stand-location-schedules/{standLocationSchedule}', [StandLocationScheduleController::class, 'destroy'])->name('admin.schedules.destroy');
    Route::patch('/stand-location-schedules/{standLocationSchedule}/set-current', [StandLocationScheduleController::class, 'setCurrent'])->name('admin.schedules.setCurrent');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
