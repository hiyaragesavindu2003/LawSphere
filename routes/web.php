<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LawyerApprovalController as AdminLawyerApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Client\LegalAdviceController as ClientLegalAdviceController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Client\ChatController as ClientChatController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LawyerController;
use App\Http\Controllers\Lawyer\MembershipController as LawyerMembershipController;
use App\Http\Controllers\Lawyer\LegalAdviceController as LawyerLegalAdviceController;
use App\Http\Controllers\Lawyer\AppointmentController as LawyerAppointmentController;
use App\Http\Controllers\Lawyer\ChatController as LawyerChatController;
use App\Http\Controllers\Lawyer\DashboardController as LawyerDashboardController;
use App\Http\Middleware\EnsureLawyerApproved;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/lawyers', [LawyerController::class, 'index'])->name('lawyers.index');
Route::get('/lawyers/{lawyer}', [LawyerController::class, 'show'])->name('lawyers.show');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register/client', [RegisteredUserController::class, 'createClient'])->name('register.client');
    Route::post('register/client', [RegisteredUserController::class, 'storeClient']);

    Route::get('register/lawyer', [RegisteredUserController::class, 'createLawyer'])->name('register.lawyer');
    Route::post('register/lawyer', [RegisteredUserController::class, 'storeLawyer']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('password/change', [PasswordController::class, 'update'])->name('password.update');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('payments/{payment}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
});

Route::middleware('auth')->group(function () {
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/lawyers', [AdminLawyerApprovalController::class, 'index'])->name('lawyers.index');
        Route::get('/lawyers/{lawyer}', [AdminLawyerApprovalController::class, 'show'])->name('lawyers.show');
        Route::patch('/lawyers/{lawyer}/approve', [AdminLawyerApprovalController::class, 'approve'])->name('lawyers.approve');
        Route::delete('/lawyers/{lawyer}/reject', [AdminLawyerApprovalController::class, 'reject'])->name('lawyers.reject');
    });

    Route::middleware(['role:lawyer'])->prefix('lawyer')->name('lawyer.')->group(function () {
        Route::get('/pending-approval', [LawyerDashboardController::class, 'pendingApproval'])
            ->name('pending-approval');

        Route::middleware([EnsureLawyerApproved::class])->group(function () {
            Route::get('/dashboard', [LawyerDashboardController::class, 'index'])->name('dashboard');

            Route::get('/messages', [LawyerChatController::class, 'index'])->name('chat.index');
            Route::get('/messages/{conversation}', [LawyerChatController::class, 'show'])->name('chat.show');
            Route::post('/messages/{conversation}', [LawyerChatController::class, 'store'])->name('chat.store');
            Route::get('/messages/{conversation}/fetch', [LawyerChatController::class, 'fetch'])->name('chat.fetch');

            Route::get('/appointments', [LawyerAppointmentController::class, 'index'])->name('appointments.index');
            Route::get('/appointments/{appointment}', [LawyerAppointmentController::class, 'show'])->name('appointments.show');
            Route::patch('/appointments/{appointment}/approve', [LawyerAppointmentController::class, 'approve'])->name('appointments.approve');
            Route::patch('/appointments/{appointment}/reject', [LawyerAppointmentController::class, 'reject'])->name('appointments.reject');
            Route::patch('/appointments/{appointment}/reschedule', [LawyerAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
            Route::patch('/appointments/{appointment}/complete', [LawyerAppointmentController::class, 'complete'])->name('appointments.complete');

            Route::get('/legal-advice', [LawyerLegalAdviceController::class, 'index'])->name('legal-advice.index');
            Route::get('/legal-advice/{legalRequest}', [LawyerLegalAdviceController::class, 'show'])->name('legal-advice.show');
            Route::post('/legal-advice/{legalRequest}/respond', [LawyerLegalAdviceController::class, 'respond'])->name('legal-advice.respond');
            Route::patch('/legal-advice/{legalRequest}/resolve', [LawyerLegalAdviceController::class, 'resolve'])->name('legal-advice.resolve');

            Route::get('/membership', [LawyerMembershipController::class, 'index'])->name('membership.index');
            Route::post('/membership/{plan}/subscribe', [LawyerMembershipController::class, 'subscribe'])->name('membership.subscribe');
        });
    });

    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');

        Route::get('/messages', [ClientChatController::class, 'index'])->name('chat.index');
        Route::post('/messages/start/{lawyer}', [ClientChatController::class, 'start'])->name('chat.start');
        Route::get('/messages/{conversation}', [ClientChatController::class, 'show'])->name('chat.show');
        Route::post('/messages/{conversation}', [ClientChatController::class, 'store'])->name('chat.store');
        Route::get('/messages/{conversation}/fetch', [ClientChatController::class, 'fetch'])->name('chat.fetch');

        Route::get('/appointments', [ClientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/book/{lawyer}', [ClientAppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments/book/{lawyer}', [ClientAppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [ClientAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/cancel', [ClientAppointmentController::class, 'cancel'])->name('appointments.cancel');

        Route::get('/legal-advice', [ClientLegalAdviceController::class, 'index'])->name('legal-advice.index');
        Route::get('/legal-advice/ask/{lawyer}', [ClientLegalAdviceController::class, 'create'])->name('legal-advice.create');
        Route::post('/legal-advice/ask/{lawyer}', [ClientLegalAdviceController::class, 'store'])->name('legal-advice.store');
        Route::get('/legal-advice/{legalRequest}', [ClientLegalAdviceController::class, 'show'])->name('legal-advice.show');
        Route::patch('/legal-advice/{legalRequest}/close', [ClientLegalAdviceController::class, 'close'])->name('legal-advice.close');
    });
});
