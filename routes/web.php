<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BoatOperatorController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/boat', [BoatOperatorController::class, 'index'])->name('boats.index');
Route::get('/boat/schedules', [BoatOperatorController::class, 'schedules'])->name('boats.schedules');
Route::get('/boat/vessel/{vessel}', [BoatOperatorController::class, 'vessel'])->name('boats.vessel');
Route::get('/boat/{boat}/order', [BoatOperatorController::class, 'order'])->name('boats.order');
Route::post('/boat/{boat}/order', [BookingController::class, 'storeBoat'])->name('boats.book');

Route::get('/activity', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/activity/explore', [ActivityController::class, 'explore'])->name('activities.explore');
Route::get('/activity/{activity}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/activity/{activity}/order', [ActivityController::class, 'order'])->name('activities.order');
Route::post('/activity/{activity}/order', [BookingController::class, 'storeActivity'])->name('activities.book');

Route::get('/hotel', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotel/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
Route::get('/hotel/{hotel}/order', [HotelController::class, 'order'])->name('hotels.order');
Route::post('/hotel/{hotel}/order', [BookingController::class, 'storeHotel'])->name('hotels.book');

Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('bookings.show');

Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('newsletter.store');

// Admin console — Figma frames 1:6642 and siblings.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\Auth\LoginController::class, 'create'])->name('login');
        Route::post('/login', [Admin\Auth\LoginController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [Admin\Auth\LoginController::class, 'destroy'])->name('logout');

        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/boat', [Admin\VesselController::class, 'index'])->name('boats');
        Route::get('/boat/create', [Admin\VesselController::class, 'create'])->name('boats.create');
        Route::post('/boat', [Admin\VesselController::class, 'store'])->name('boats.store');
        Route::get('/boat/{vessel}/edit', [Admin\VesselController::class, 'edit'])->name('boats.edit');
        Route::put('/boat/{vessel}', [Admin\VesselController::class, 'update'])->name('boats.update');
        Route::delete('/boat', [Admin\VesselController::class, 'bulkDestroy'])->name('boats.bulk-destroy');
        Route::delete('/boat/{vessel}', [Admin\VesselController::class, 'destroy'])->name('boats.destroy');

        Route::get('/schedule', [Admin\ScheduleController::class, 'index'])->name('schedules');
        Route::get('/schedule/create', [Admin\ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedule', [Admin\ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedule/{schedule}/edit', [Admin\ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedule/{schedule}', [Admin\ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedule', [Admin\ScheduleController::class, 'bulkDestroy'])->name('schedules.bulk-destroy');
        Route::delete('/schedule/{schedule}', [Admin\ScheduleController::class, 'destroy'])->name('schedules.destroy');

        Route::get('/activity', [Admin\ActivityController::class, 'index'])->name('activities');
        Route::get('/activity/create', [Admin\ActivityController::class, 'create'])->name('activities.create');
        Route::post('/activity', [Admin\ActivityController::class, 'store'])->name('activities.store');
        Route::get('/activity/{activity}/edit', [Admin\ActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/activity/{activity}', [Admin\ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activity', [Admin\ActivityController::class, 'bulkDestroy'])->name('activities.bulk-destroy');
        Route::delete('/activity/{activity}', [Admin\ActivityController::class, 'destroy'])->name('activities.destroy');

        Route::get('/hotel', [Admin\HotelController::class, 'index'])->name('hotels');
        Route::get('/hotel/create', [Admin\HotelController::class, 'create'])->name('hotels.create');
        Route::post('/hotel', [Admin\HotelController::class, 'store'])->name('hotels.store');
        Route::get('/hotel/{hotel}/edit', [Admin\HotelController::class, 'edit'])->name('hotels.edit');
        Route::put('/hotel/{hotel}', [Admin\HotelController::class, 'update'])->name('hotels.update');
        Route::delete('/hotel/{hotel}', [Admin\HotelController::class, 'destroy'])->name('hotels.destroy');

        Route::get('/article', [Admin\ArticleController::class, 'index'])->name('articles');
        Route::get('/article/create', [Admin\ArticleController::class, 'create'])->name('articles.create');
        Route::post('/article/image', [Admin\ArticleController::class, 'uploadImage'])->name('articles.image');
        Route::post('/article', [Admin\ArticleController::class, 'store'])->name('articles.store');
        Route::get('/article/{article}/edit', [Admin\ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/article/{article}', [Admin\ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/article', [Admin\ArticleController::class, 'bulkDestroy'])->name('articles.bulk-destroy');
        Route::delete('/article/{article}', [Admin\ArticleController::class, 'destroy'])->name('articles.destroy');

        Route::get('/report', [Admin\ReportController::class, 'index'])->name('report');
        Route::get('/report/export', [Admin\ReportController::class, 'export'])->name('report.export');
        Route::patch('/report/{booking}', [Admin\ReportController::class, 'update'])->name('report.update');
    });
});
