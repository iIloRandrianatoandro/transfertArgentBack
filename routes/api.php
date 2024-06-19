<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Authentification;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//compte
Route::controller(CompteController::class)->group(function(){
    Route::get('associerCompte','associerCompte'); 
    Route::get('modifierCompte','modifierCompte'); 
    Route::get('consulterCompte','consulterCompte'); 
});
//notification
Route::controller(NotificationController::class)->group(function(){
    Route::get('creerNotification','creerNotification'); 
    Route::get('consulterNotification','consulterNotification'); 
    Route::get('supprimerNotification','supprimerNotification'); 
});
//transaction
Route::controller(TransactionController::class)->group(function(){
    Route::get('creerTransaction','creerTransaction'); 
    Route::get('consulterTransaction','consulterTransaction'); 
});
//authentification
Route::controller(authentification::class)->group(function(){
    Route::post('seConnecter','seConnecter');
    Route::get('seDeconnecter','seDeconnecter');
    Route::post('sInscrire','sInscrire');
    Route::post('modifierProfil/{id}','modifierProfil');
    Route::post('supprimerProfil/{id}','supprimerProfil');
    Route::get('consulterProfil/{id}','consulterProfil');
});
