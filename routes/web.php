<?php

use App\Livewire\Dashboard;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\User\Create as UserCreate;
use App\Livewire\User\Edit as UserEdit;
use App\Livewire\User\Index as UserIndex;
use App\Livewire\User\Show as UserShow;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

// Route::post('upload-file', [App\Http\Controllers\API\UserAPIController::class, 'uploadFile'])->name('uploadFile');
Route::post('store-flash-message', function (Illuminate\Http\Request $request) {
    $data = $request->json()->all();
    session()->put('livewire_flash_type', $data['type'] ?? 'info');
    session()->put('livewire_flash_message', $data['message'] ?? '');

    return response()->json(['success' => true]);
})->name('store-flash-message');

Route::middleware(['auth'])->group(function () {
    Route::post('upload-file', [App\Http\Controllers\API\UserAPIController::class, 'uploadFile'])->name('uploadFile');

    Route::get('dashboard', Dashboard::class)->name('dashboard');
    // Admin - Role Module
    Route::get('/role', App\Livewire\Role\Index::class)->name('role.index'); // Role Listing
    Route::get('/role/create', App\Livewire\Role\Create::class)->name('role.add'); // Create Role
    Route::get('/role/{id}/edit', App\Livewire\Role\Edit::class)->name('role.edit'); // Edit Role
    Route::get('/role-imports', App\Livewire\Role\Import\IndexImport::class)->name('role.imports'); // Import history

    // Permission Management
    Route::get('permission', App\Livewire\Permission\Edit::class)->name('permission');

    // User Management
    Route::get('users', UserIndex::class)->name('users.index');
    Route::get('users/create', UserCreate::class)->name('users.create');
    Route::get('users/{id}/edit', UserEdit::class)->name('users.edit');
    Route::get('users/{id}/view', UserShow::class)->name('users.show');
    Route::get('users-imports', App\Livewire\User\Import\IndexImport::class)->name('users.imports'); // User Import History

    // Settings
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');

    /* Admin - Brand Module */
    Route::get('/brand', App\Livewire\Brand\Index::class)->name('brand.index'); // Brand Listing
    Route::get('/brand/create', App\Livewire\Brand\Create::class)->name('brand.add'); // Create Brand
    Route::get('/brand/{id}/edit', App\Livewire\Brand\Edit::class)->name('brand.edit'); // Edit Brand

    /* Email Format and Template Modules */
    Route::get('/email-format', App\Livewire\EmailFormat\Edit::class)->name('email-format'); // Email Format Edit
    Route::get('/email-templates', App\Livewire\EmailTemplate\Index::class)->name('email-template.index'); // Email Template Listing
    Route::get('/email-template/{id}/edit', App\Livewire\EmailTemplate\Edit::class)->name('email-template.edit'); // Edit Email Template
});
