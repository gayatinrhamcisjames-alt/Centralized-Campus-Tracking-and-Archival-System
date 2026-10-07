<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpCenterAdminController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThesisController;
use App\Http\Controllers\ThesisDocumentController;
use App\Http\Controllers\ThesisMilestoneController;
use App\Http\Controllers\ThesisWorkflowController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:Administrator')
        ->name('admin.dashboard');
    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('role:Administrator');
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])
        ->middleware('role:Staff')
        ->name('staff.dashboard');
    Route::get('/coordinator/dashboard', [DashboardController::class, 'coordinator'])
        ->middleware('role:Thesis Coordinator')
        ->name('coordinator.dashboard');
    Route::get('/adviser/dashboard', [DashboardController::class, 'adviser'])
        ->middleware('role:Faculty Adviser')
        ->name('adviser.dashboard');
    Route::get('/panel/dashboard', [DashboardController::class, 'panel'])
        ->middleware('role:Panel Member')
        ->name('panel.dashboard');
    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->middleware('role:Student')
        ->name('student.dashboard');
    Route::get('/workflow', [DashboardController::class, 'workflow'])->name('workflow.index');
    Route::get('/analytics', [DashboardController::class, 'analyticsCenter'])->name('analytics.index');
    Route::get('/support', [HelpCenterController::class, 'index'])->name('support.index');
    Route::get('/support/search', [HelpCenterController::class, 'suggestions'])
        ->middleware('throttle:30,1')
        ->name('support.search');
    Route::get('/support/requests', [HelpCenterController::class, 'requests'])->name('support.requests');
    Route::get('/support/guides/{article:slug}', [HelpCenterController::class, 'show'])->name('support.articles.show');
    Route::post('/support/guides/{article:slug}/feedback', [HelpCenterController::class, 'feedback'])
        ->middleware('throttle:20,1')
        ->name('support.articles.feedback');
    Route::post('/support/tickets', [HelpCenterController::class, 'ticket'])
        ->middleware('throttle:5,1')
        ->name('support.tickets.store');
    Route::post('/support/issues', [HelpCenterController::class, 'issue'])
        ->middleware('throttle:5,1')
        ->name('support.issues.store');
    Route::get('/support/admin/attachments/{type}/{id}', [HelpCenterController::class, 'attachment'])
        ->middleware('role:Administrator')
        ->name('support.admin.attachments.show');
    Route::prefix('/support/admin')->name('support.admin.')->middleware('role:Administrator')->group(function () {
        Route::get('/', [HelpCenterAdminController::class, 'index'])->name('index');
        Route::post('/articles', [HelpCenterAdminController::class, 'storeArticle'])->name('articles.store');
        Route::put('/articles/{article}', [HelpCenterAdminController::class, 'updateArticle'])->name('articles.update');
        Route::delete('/articles/{article}', [HelpCenterAdminController::class, 'destroyArticle'])->name('articles.destroy');
        Route::post('/categories', [HelpCenterAdminController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [HelpCenterAdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [HelpCenterAdminController::class, 'destroyCategory'])->name('categories.destroy');
        Route::post('/faqs', [HelpCenterAdminController::class, 'storeFaq'])->name('faqs.store');
        Route::put('/faqs/{faq}', [HelpCenterAdminController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [HelpCenterAdminController::class, 'destroyFaq'])->name('faqs.destroy');
        Route::patch('/tickets/{ticket}', [HelpCenterAdminController::class, 'updateTicket'])->name('tickets.update');
        Route::patch('/issues/{issue}', [HelpCenterAdminController::class, 'updateIssue'])->name('issues.update');
    });
    Route::post('/weather/refresh', [DashboardController::class, 'refreshWeather'])
        ->middleware('throttle:6,1')
        ->name('weather.refresh');
    Route::get('/theses/archive', [ThesisController::class, 'archive'])->name('theses.archive');
    Route::resource('theses', ThesisController::class);
    Route::post('/theses/{thesis}/archive', [ThesisWorkflowController::class, 'archive'])
        ->middleware('role:Administrator,Staff,Thesis Coordinator')
        ->name('theses.archive.store');
    Route::post('/theses/{thesis}/restore', [ThesisWorkflowController::class, 'restore'])
        ->middleware('role:Administrator,Staff,Thesis Coordinator')
        ->name('theses.restore');
    Route::post('/theses/{thesis}/reviewers', [ThesisWorkflowController::class, 'assignReviewer'])
        ->middleware('role:Administrator,Thesis Coordinator')
        ->name('theses.reviewers.store');
    Route::post('/theses/{thesis}/reviews', [ThesisWorkflowController::class, 'review'])
        ->middleware('role:Staff,Faculty Adviser,Panel Member')
        ->name('theses.reviews.store');
    Route::post('/theses/{thesis}/documents', [ThesisDocumentController::class, 'store'])->name('theses.documents.store');
    Route::post('/theses/{thesis}/documents/{document}/versions', [ThesisDocumentController::class, 'storeVersion'])->name('theses.documents.versions.store');
    Route::get('/theses/{thesis}/documents/versions/{version}/download', [ThesisDocumentController::class, 'download'])->name('theses.documents.versions.download');
    Route::post('/theses/{thesis}/milestones', [ThesisMilestoneController::class, 'store'])->name('theses.milestones.store');
    Route::put('/theses/{thesis}/milestones/{milestone}', [ThesisMilestoneController::class, 'update'])->name('theses.milestones.update');
    Route::delete('/theses/{thesis}/milestones/{milestone}', [ThesisMilestoneController::class, 'destroy'])->name('theses.milestones.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::view('/about', 'about.index')->name('about.index');
});

require __DIR__.'/auth.php';
