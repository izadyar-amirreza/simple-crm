<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Carbon;


Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------|
| Dashboard
|--------------------------------------------------------------------------|
*/
Route::get('/dashboard', function () {

    $user = auth()->user();

    $overdueTasks = \App\Models\Task::query()
        ->visibleTo($user)
        ->where('status', 'open')
        ->whereNotNull('due_at')
        ->where('due_at', '<', now())
        ->orderBy('due_at')
        ->limit(5)
        ->get();

    $todayTasks = \App\Models\Task::query()
        ->visibleTo($user)
        ->where('status', 'open')
        ->whereNotNull('due_at')
        ->whereDate('due_at', now()->toDateString())
        ->orderBy('due_at')
        ->limit(10)
        ->get();

    return view('dashboard', compact('overdueTasks', 'todayTasks'));

})->middleware(['auth', 'verified', 'permission:dashboard.view'])
  ->name('dashboard');


/*
|--------------------------------------------------------------------------|
| Authenticated routes
|--------------------------------------------------------------------------|
*/
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customers (extra actions)
    Route::get('/customers-trash', [CustomerController::class, 'trash'])->name('customers.trash');
    Route::post('/customers/{id}/restore', [CustomerController::class, 'restore'])->name('customers.restore');
    Route::delete('/customers/{id}/force-delete', [CustomerController::class, 'forceDelete'])->name('customers.forceDelete');
    Route::get('/activity', [\App\Http\Controllers\ActivityLogController::class, 'index'])
    ->middleware('permission:activity.view')
    ->name('activity.index');


    // Customers CRUD
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{id}/activity', [CustomerController::class, 'activity'])
    ->name('customers.activity');

     // Lead convert
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])
        ->name('leads.convert');

    // Leads CRUD
    Route::resource('leads', LeadController::class);
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    Route::patch('tasks/{task}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])
    ->name('tasks.status');

    // Leads Trash
    Route::get('/leads-trash', [LeadController::class, 'trash'])->name('leads.trash');
    Route::post('/leads/{id}/restore', [LeadController::class, 'restore'])->name('leads.restore');
    Route::delete('/leads/{id}/force-delete', [LeadController::class, 'forceDelete'])->name('leads.forceDelete');

    // Tasks Trash
    Route::get('/tasks-trash', [TaskController::class, 'trash'])->name('tasks.trash');
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('/tasks/{id}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');

});

require __DIR__.'/auth.php';
