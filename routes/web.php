<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CRM\Calls\CallsController;
use App\Http\Controllers\CRM\Dashboard\DashboardController;
use App\Http\Controllers\CRM\Whatsapp\WhatsappController;
use App\Http\Controllers\CRM\Leads\AllLeadsController;
use App\Http\Controllers\CRM\Leads\AddLeadController;
use App\Http\Controllers\CRM\Followups\FollowupController;
use App\Http\Controllers\CRM\Reports\LeadReportController;
use App\Http\Controllers\CRM\Reports\AdmissionReportController;
use App\Http\Controllers\CRM\Reports\UserWiseAdmissionReportController;
use App\Http\Controllers\CRM\Reports\SourceWiseReportController;
use App\Http\Controllers\CRM\Reports\FollowupReportController;
use App\Http\Controllers\CRM\Reports\PendingFollowupReportController;
use App\Http\Controllers\CRM\Reports\CourseWiseReportController;
use App\Http\Controllers\CRM\Reports\StandardWiseReportController;
use App\Http\Controllers\CRM\Reports\LostLeadReportController;
use App\Http\Controllers\CRM\Reports\ConversionReportController;
use App\Http\Controllers\CRM\Reports\CounsellorPerformanceReportController;
use App\Http\Controllers\CRM\Reports\FeeQuotationReportController;
use App\Http\Controllers\CRM\Reports\ReEngagementReportController;
use App\Http\Controllers\CRM\Settings\UserController as CrmUserController;
use App\Http\Controllers\CRM\Settings\StandardController;
use App\Http\Controllers\CRM\Settings\FollowupTypeController;
use App\Http\Controllers\CRM\Settings\CourseController;
use App\Http\Controllers\CRM\Settings\LeadStatusController;
use App\Http\Controllers\CRM\Settings\LeadSourceController;
use App\Http\Controllers\CRM\Settings\LeadPriorityController;
use App\Http\Controllers\CRM\Settings\WhatsappTemplateController;
use App\Http\Controllers\CRM\Settings\AppSettingController;
use App\Http\Controllers\CRM\Settings\ClosedLeadStatusController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('crm.auth.index');
})->name('login');

Route::post('/', [AuthController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| API ROUTES FOR MOBILE APP (NO AUTH FOR TESTING)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/calls/incoming', [CallsController::class, 'storeFromMobile'])->name('calls.incoming');
    Route::get('/calls/pending', [CallsController::class, 'getPendingCalls'])->name('calls.pending');
    Route::post('/whatsapp/log', [WhatsappController::class, 'storeFromMobile'])->name('whatsapp.log');
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (REQUIRE AUTHENTICATION)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('calls')->name('calls.')->group(function () {
        Route::get('/', [CallsController::class, 'index'])->name('index');
        Route::get('/{call}', [CallsController::class, 'show'])->name('show');
        Route::patch('/{call}/update-status', [CallsController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/{call}/update-name', [CallsController::class, 'updateName'])->name('updateName');
        Route::delete('/{call}', [CallsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/', [WhatsappController::class, 'index'])->name('index');
        Route::get('/{whatsapp}', [WhatsappController::class, 'show'])->name('show');
        Route::delete('/{whatsapp}', [WhatsappController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('/', [AllLeadsController::class, 'index'])->name('index');
        Route::get('/add', [AddLeadController::class, 'create'])->name('create');
        Route::post('/', [AddLeadController::class, 'store'])->name('store');
        Route::get('/closed', [AllLeadsController::class, 'closed'])->name('closed');
        Route::post('/bulk-action', [AllLeadsController::class, 'bulkAction'])->name('bulk');
        Route::post('/convert-call/{call}', [AllLeadsController::class, 'convertFromCall'])->name('convert.call');
        Route::post('/convert-whatsapp/{whatsapp}', [AllLeadsController::class, 'convertFromWhatsapp'])->name('convert.whatsapp');
        Route::get('/quotation/{quotation}/print', [AllLeadsController::class, 'printQuotation'])->name('quotation.print');
        Route::get('/{lead}', [AllLeadsController::class, 'show'])->name('show');
        Route::get('/{lead}/edit', [AllLeadsController::class, 'edit'])->name('edit');
        Route::put('/{lead}', [AllLeadsController::class, 'update'])->name('update');
        Route::delete('/{lead}', [AllLeadsController::class, 'destroy'])->name('destroy');
        Route::post('/{lead}/quotation', [AllLeadsController::class, 'storeQuotation'])->name('quotation.store');
        Route::get('/{lead}/admission-form/print', [AllLeadsController::class, 'admissionForm'])->name('admission-form');
    });

    Route::prefix('followups')->name('followups.')->group(function () {
    Route::get('/', [FollowupController::class, 'index'])->name('index');
    Route::post('/lead/{lead}', [FollowupController::class, 'store'])->name('store');
    Route::patch('/{followup}/complete', [FollowupController::class, 'complete'])->name('complete');
    Route::patch('/{followup}/close-lead', [FollowupController::class, 'closeLead'])->name('closeLead');
    Route::delete('/{followup}', [FollowupController::class, 'destroy'])->name('destroy');
    Route::patch('/{followup}/change-type', [FollowupController::class, 'changeType'])->name('changeType');
    Route::post('/{followup}/next-followup', [FollowupController::class, 'createNextFollowup'])->name('createNextFollowup');
});

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/lead', [LeadReportController::class, 'index'])->name('lead');
        Route::get('/admission', [AdmissionReportController::class, 'index'])->name('admission');
        Route::get('/user-wise-admission', [UserWiseAdmissionReportController::class, 'index'])->name('user-wise-admission');
        Route::get('/source-wise', [SourceWiseReportController::class, 'index'])->name('source-wise');
        Route::get('/followup', [FollowupReportController::class, 'index'])->name('followup');
        Route::get('/pending-followup', [PendingFollowupReportController::class, 'index'])->name('pending-followup');
        Route::get('/course-wise', [CourseWiseReportController::class, 'index'])->name('course-wise');
        Route::get('/standard-wise', [StandardWiseReportController::class, 'index'])->name('standard-wise');
        Route::get('/lost-lead', [LostLeadReportController::class, 'index'])->name('lost-lead');
        Route::get('/conversion', [ConversionReportController::class, 'index'])->name('conversion');
        Route::get('/counsellor-performance', [CounsellorPerformanceReportController::class, 'index'])->name('counsellor-performance');
        Route::get('/fee-quotation', [FeeQuotationReportController::class, 'index'])->name('fee-quotation');
        Route::get('/re-engagement', [ReEngagementReportController::class, 'index'])->name('re-engagement');
    });

   Route::prefix('settings')->name('settings.')->group(function () {
    Route::resource('users', CrmUserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('standards', StandardController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('followup-types', FollowupTypeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('courses', CourseController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('lead-statuses', LeadStatusController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('lead-sources', LeadSourceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('lead-priorities', LeadPriorityController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('whatsapp-templates', WhatsappTemplateController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('app-settings', [AppSettingController::class, 'index'])
        ->name('app-settings.index');

    Route::post('app-settings', [AppSettingController::class, 'store'])
        ->name('app-settings.store');

    Route::put('app-settings', [AppSettingController::class, 'store'])
        ->name('app-settings.update');
        Route::resource('closed-statuses', ClosedLeadStatusController::class)
    ->only(['index', 'store', 'update', 'destroy']);
});
});
