<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CocController;
use App\Http\Controllers\CtplController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ComprehensiveController;

// --- Public Routes ---
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

// --- Authenticated Dashboard & Profile ---
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // CTPL Issuance Form
    Route::get('/ctpl-issuance', [CtplController::class, 'index'])->name('admin.ctpl.index');
    
    // Saved Transactions (The Datatable Page)
    Route::get('/saved-transactions', [CtplController::class, 'savedTransactions'])->name('admin.saved_transactions');

    Route::prefix('user/profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });
});

// --- COC Management ---
Route::middleware(['auth'])->prefix('coc-management')->name('admin.coc.')->group(function () {
    Route::get('/', [CocController::class, 'index'])->name('index'); 
    Route::post('/series-upload', [CocController::class, 'seriesUpload'])->name('seriesUpload');
    Route::post('/upload-csv', [CocController::class, 'upload'])->name('upload');
    Route::get('/preview', [CocController::class, 'previewSeries'])->name('previewSeries');
    Route::delete('/series-delete', [CocController::class, 'seriesDelete'])->name('seriesDelete');
});

// --- Admin Post/Action Routes ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // CTPL Actions (Store, Print, View, Edit, Search)
    Route::group(['prefix' => 'ctpl', 'as' => 'ctpl.'], function () {
        Route::post('/store', [CtplController::class, 'store'])->name('store');
        Route::get('/search-vehicle', [CtplController::class, 'searchVehicle'])->name('search_vehicle');
        
        // Transaction Actions
        Route::get('/view/{id}', [CtplController::class, 'show'])->name('view');
        Route::get('/edit/{id}', [CtplController::class, 'edit'])->name('edit'); // Added Edit to CtplController
        Route::put('/update/{id}', [CtplController::class, 'update'])->name('update'); // Added Update to CtplController
        Route::get('/print/{id}', [CtplController::class, 'showPrint'])->name('print');
    });

    Route::resource('comprehensive', ComprehensiveController::class);
});