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
    Route::post('associerCompte/{userId}','associerCompte'); 
    Route::get('listerCompteExpediteur/{idUser}','listerCompteExpediteur'); 
    Route::get('listerCompteDestinataire/{idUser}','listerCompteDestinataire'); 
    Route::get('consulterCompte/{id}','consulterCompte'); 
    Route::delete('supprimerCompte/{id}','supprimerCompte'); 
});
//notification
Route::controller(NotificationController::class)->group(function(){
    Route::get('markAsRead/{id}/{idNotification}','markAsRead'); 
    Route::get('listeNotificationNonLues/{id}','listeNotificationNonLues'); 
    Route::get('listeNotificationLues/{id}','listeNotificationLues'); 
    Route::get('supprimerNotification/{idNotification}','supprimerNotification'); 
});
//transaction
Route::controller(TransactionController::class)->group(function(){
    Route::post('creerTransaction/{idUser}','creerTransaction'); 
    Route::get('listerTransaction/{idUser}','listerTransaction'); 
    Route::get('consulterTransaction/{idTransaction}','consulterTransaction'); 
});
//authentification
Route::controller(authentification::class)->group(function(){
    Route::post('seConnecter','seConnecter');
    Route::get('seDeconnecter','seDeconnecter');
    Route::post('sInscrire','sInscrire');
    Route::post('modifierProfil/{id}','modifierProfil');
    Route::post('ajouterInformation/{id}','ajouterInformation');
    Route::post('supprimerProfil/{id}','supprimerProfil');
    Route::get('consulterProfil/{id}','consulterProfil');
    //google authentification
    Route::get('googleAuthentification','redirectToGoogle');
    Route::get('googleCallback','handleGoogleCallback');
    //verfier numero de telephone 
    Route::post('envoyerCodeSmsVerificationTelephone','envoyerCodeSmsVerificationTelephone');
    Route::post('verifierCode/{code}','verifierCode');
    //verifier adresse mail
    Route::post('verifierMail','verifierMail');

});
