<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('notification/send/email/attachment', [\App\Http\Controllers\EmailSendinBlueController::class, 'sendEmailAttachment']);
Route::post('notification/send/transactional/email', [\App\Http\Controllers\EmailSendinBlueController::class, 'sendTransactionalEmail']);
Route::post('notification/send/campaign/email', [\App\Http\Controllers\EmailSendinBlueController::class, 'sendEmail']);
Route::post('notification/create/campaign', [\App\Http\Controllers\EmailSendinBlueController::class, 'createCampaign']);
Route::post('notification/get/campaign', [\App\Http\Controllers\EmailSendinBlueController::class, 'getCampaign']);
Route::post('notification/delete/campaign', [\App\Http\Controllers\EmailSendinBlueController::class, 'deleteCampaign']);
Route::post('notification/update/campaign', [\App\Http\Controllers\EmailSendinBlueController::class, 'updateCampaign']);
Route::post('notification/account/information', [\App\Http\Controllers\EmailSendinBlueController::class, 'accountInformation']);
Route::get('notification/get/contacts', [\App\Http\Controllers\EmailSendinBlueController::class, 'getUpdateContacts']);
Route::post('notification/create/contact', [\App\Http\Controllers\EmailSendinBlueController::class, 'createContact']);
Route::post('notification/create/contacts', [\App\Http\Controllers\EmailSendinBlueController::class, 'createContacts']);
Route::post('notification/import/contacts', [\App\Http\Controllers\EmailSendinBlueController::class, 'importContacts']);
Route::post('notification/export/contacts', [\App\Http\Controllers\EmailSendinBlueController::class, 'exportContacts']);
Route::post('notification/delete/contact', [\App\Http\Controllers\EmailSendinBlueController::class, 'deleteContact']);
Route::post('notification/delete/contacts', [\App\Http\Controllers\EmailSendinBlueController::class, 'deleteContacts']);
Route::post('notification/update/contact', [\App\Http\Controllers\EmailSendinBlueController::class, 'updateContact']);
Route::get('notification/contact/statistics', [\App\Http\Controllers\EmailSendinBlueController::class, 'contactCampaignsStatistics']);
Route::post('notification/all/list', [\App\Http\Controllers\EmailSendinBlueController::class, 'getAllList']);
Route::post('notification/create/list', [\App\Http\Controllers\EmailSendinBlueController::class, 'createList']);
Route::post('notification/update/list', [\App\Http\Controllers\EmailSendinBlueController::class, 'updateList']);
Route::post('notification/delete/list', [\App\Http\Controllers\EmailSendinBlueController::class, 'deleteList']);
Route::post('notification/contacts/from', [\App\Http\Controllers\EmailSendinBlueController::class, 'getContactsFromList']);
Route::post('notification/create/template', [\App\Http\Controllers\EmailSendinBlueController::class, 'createTemplate']);
Route::post('notification/get/folders', [\App\Http\Controllers\EmailSendinBlueController::class, 'getFolders']);
Route::post('notification/delete/folder', [\App\Http\Controllers\EmailSendinBlueController::class, 'deleteFolder']);
Route::post('notification/create/folder', [\App\Http\Controllers\EmailSendinBlueController::class, 'createFolder']);
Route::post('notification/email/send', [\App\Http\Controllers\EmailSendinBlueController::class, 'sendEmail']);

