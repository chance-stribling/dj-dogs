<?php

use App\Http\Controllers\ProfileController;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\StandLocation;
use App\Models\StandLocationSchedule;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BookingRequestController;
use App\Http\Controllers\ContactMessageController;


Route::post('/booking', [BookingRequestController::class, 'store'])->name('booking.store');
Route::get('/', function () {
    $current = StandLocationSchedule::with('standLocation')->current()->first();

    return Inertia::render('Landing', [
        'menuItems'         => MenuItem::where('available', true)->get(),
        'categories'        => MenuCategory::all(),
        'currentLocation'   => $current?->standLocation ? $current : null,
        'upcomingLocations' => StandLocationSchedule::with('standLocation')->upcoming()->get(),
    ]);
})->name('landing');


Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

Route::get('/contact', function () {
    return Inertia::render('Contact', []);
})->name('contact');

Route::get('/book', function () {
    return Inertia::render('Book', []);
})->name('book');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
