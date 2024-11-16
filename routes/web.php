<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\MoneyRequestController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\MoneyInController;
use App\Http\Controllers\MoneyOutController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    } elseif ($user->hasRole('owner')) {
        return redirect()->route('owner.dashboard');
    } elseif ($user->hasRole('supervisor')) {
        return redirect()->route('supervisor.dashboard');
    } elseif ($user->hasRole('account manager')) {
        return redirect()->route('account-manager.dashboard');
    } else {
        return redirect('/'); 
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dynamic-dashboard', function () {
    $user = Auth::user();
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('manager')) {
        return redirect()->route('manager.dashboard');
    } elseif ($user->hasRole('owner')) {
        return redirect()->route('owner.dashboard');
    } elseif ($user->hasRole('supervisor')) {
        return redirect()->route('supervisor.dashboard');
    } elseif ($user->hasRole('account manager')) {
        return redirect()->route('account-manager.dashboard');
    } else {
        return redirect('/'); 
    }
})->middleware('auth')->name('dynamic-dashboard');

Route::get('/projects/{project}/generate-pdf', [ProjectsController::class, 'generatePdf'])->name('projects.generatePdf');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('projects.challans', ChallanController::class)->except(['edit', 'update', 'destroy', 'store']);
    Route::patch('/challans/{challan}/status', [ChallanController::class, 'updateStatus'])->name('challans.updateStatus');
    
    Route::get('/owner/user-list', [OwnerController::class, 'getUserList'])->name('owner.user_list');
    Route::get('/manager/user-list', [ManagerController::class, 'getUserList'])->name('manager.user_list');
    Route::get('/account-manager/user-list', [AccountManagerController::class, 'getUserList'])->name('account-manager.user_list');
    Route::get('/money-in/create', [MoneyInController::class, 'create'])->name('money_in.create');
    Route::post('/money-in', [MoneyInController::class, 'store'])->name('money_in.store');
    Route::get('/money-out/create', [MoneyOutController::class, 'create'])->name('money_out.create');
    Route::post('/money-out', [MoneyOutController::class, 'store'])->name('money_out.store');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/get-users', [AdminController::class, 'getUsers'])->name('admin.get_users');
        Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/admin/users/edit/{id}', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::patch('/admin/users/update/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::post('/admin/users/change-password/{id}', [AdminController::class, 'changePassword'])->name('admin.users.change_password');
        Route::get('/admin/users/change-password/{id}', [AdminController::class, 'showChangePasswordForm'])->name('admin.users.change_password_form');
    });

    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager/home', [ManagerController::class, 'index'])->name('manager.home');
        Route::get('/manager/dashboard', [ManagerController::class, 'home'])->name('manager.dashboard');
        Route::get('/manager/finished-projects', [ManagerController::class, 'finishedProjects'])->name('manager.finishedProjects');
        Route::resource('projects', ProjectsController::class);
        Route::get('supervisors', [ProjectsController::class, 'getSupervisors'])->name('supervisors.list');
        Route::get('/projects/{project}', [ManagerController::class, 'show'])->name('manager.projects.show');
        Route::resource('money-requests', MoneyRequestController::class)->only(['store']);
        Route::get('/manager/money-requests/pending', [MoneyRequestController::class, 'pendingRequests'])->name('manager.pending');
        Route::get('/manager/my-money-requests/pending', [MoneyRequestController::class, 'MyPendingRequests'])->name('my.pending.requests');
        Route::get('/manager/money-requests/approved', [MoneyRequestController::class, 'approvedRequests'])->name('manager.approved');
        Route::get('/manager/my-money-requests/approved', [MoneyRequestController::class, 'MyApprovedRequests'])->name('my.approved.requests');
        Route::get('/manager/money-requests/rejected', [MoneyRequestController::class, 'rejectedRequests'])->name('manager.rejected');
        Route::get('/manager/my-money-requests/rejected', [MoneyRequestController::class, 'MyRejectedRequests'])->name('my.rejected.requests');
        Route::get('/manager/money-requests/create', [MoneyRequestController::class, 'create'])->name('manager.create');
        Route::get('/manager/users/{id}/projects', [ManagerController::class, 'userProjects'])->name('manager.user_projects');
    });

    Route::middleware(['role:owner'])->group(function () {
        Route::get('/owner/dashboard', [OwnerController::class, 'pendingProjects'])->name('owner.dashboard');
        Route::get('/owner/active', [OwnerController::class, 'activeProjects'])->name('owner.dashboard.active');
        Route::get('/owner/finished', [OwnerController::class, 'finishedProjects'])->name('owner.dashboard.finished');
        Route::get('/owner/{project}', [OwnerController::class, 'show'])->name('owner.projects.show');
        Route::get('/money-requests/pending', [MoneyRequestController::class, 'pendingRequests'])->name('owner.pending');
        Route::get('/money-requests/approved', [MoneyRequestController::class, 'approvedRequests'])->name('owner.approved');
        Route::get('/money-requests/rejected', [MoneyRequestController::class, 'rejectedRequests'])->name('owner.rejected');
        
        Route::get('/owner/users/{id}/projects', [OwnerController::class, 'userProjects'])->name('owner.user_projects');
    });

    Route::middleware(['role:manager|owner|account manager|supervisor'])->group(function () {
        Route::get('projects/{id}/approve', [MoneyRequestController::class, 'approve'])->name('projects.approve');
        Route::patch('money-requests/{id}/update', [MoneyRequestController::class, 'updateStatus'])->name('money-requests.updateStatus');
        Route::get('/challans/approved', [ChallanController::class, 'approved'])->name('challans.approved');
        Route::get('/challans/pending', [ChallanController::class, 'pending'])->name('challans.pending');
        Route::get('/challans/rejected', [ChallanController::class, 'rejected'])->name('challans.rejected');
        Route::post('/projects/challans/store', [ChallanController::class, 'store'])->name('projects.challans.store');

    });

    Route::middleware(['role:supervisor'])->group(function () {
        Route::get('/supervisor/active-projects', [SupervisorController::class, 'index'])->name('supervisor.activeProjects');
        Route::get('/supervisor/projects/{project}', [SupervisorController::class, 'show'])->name('supervisor.projects.show');
        Route::get('/supervisor/finished-projects', [SupervisorController::class, 'finishedProjects'])->name('supervisor.finishedProjects');
        Route::resource('money-requests', MoneyRequestController::class)->only(['store']);
        Route::get('money-requests/create', [MoneyRequestController::class, 'create'])->name('money-requests.create');
        Route::get('challans/create', [ChallanController::class, 'create'])->name('challans.create');
        
        Route::get('/supervisor/dashboard', [SupervisorController::class, 'home'])->name('supervisor.dashboard');
        Route::get('/supervisor/money-requests/pending', [SupervisorController::class, 'pendingRequests'])->name('supervisor.moneyRequests.pending');
        Route::get('/supervisor/money-requests/approved', [SupervisorController::class, 'approvedRequests'])->name('supervisor.moneyRequests.approved');
        Route::get('/supervisor/money-requests/rejected', [SupervisorController::class, 'rejectedRequests'])->name('supervisor.moneyRequests.rejected');
    });

    Route::middleware(['role:manager|supervisor'])->group(function () {
        Route::get('challans/create', [ChallanController::class, 'create'])->name('challans.create');
        Route::resource('money-requests', MoneyRequestController::class)->only(['store']);
    });
    
    Route::middleware(['role:account manager'])->group(function () {
        Route::get('/account-manager/dashboard', [AccountManagerController::class, 'activeProjects'])->name('account-manager.dashboard');
        Route::get('/account-manager/active-projects', [AccountManagerController::class, 'activeProjects'])->name('account-manager.active-projects');
        Route::get('/account-manager/finished-projects', [AccountManagerController::class, 'finishedProjects'])->name('account-manager.finished-projects');
        Route::get('/account-manager/projects/{project}', [AccountManagerController::class, 'show'])->name('account-manager.projects.show');
        Route::get('/money-in', [AccountManagerController::class, 'index'])->name('money_in.index');
        Route::get('/money-out', [AccountManagerController::class, 'moneyOutIndex'])->name('money_out.index');
        Route::get('/account-manager/money-requests/pending', [AccountManagerController::class, 'pendingRequests'])->name('account-manager.moneyRequests.pending');
        Route::get('/account-manager/money-requests/approved', [AccountManagerController::class, 'approvedRequests'])->name('account-manager.moneyRequests.approved');
        Route::get('/account-manager/money-requests/rejected', [AccountManagerController::class, 'rejectedRequests'])->name('account-manager.moneyRequests.rejected');
        Route::get('/account-manager/users/{id}/projects', [AccountManagerController::class, 'userProjects'])->name('account-manager.user_projects');
        Route::delete('/money-requests/{id}', [MoneyRequestController::class, 'destroy'])->name('money-requests.destroy');
        Route::delete('/challans/{id}', [ChallanController::class, 'destroy'])->name('challans.destroy');

    });
});

require __DIR__.'/auth.php';
