<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use DB;
use Stripe\Stripe;
use Stripe\Account;
use App\Jobs\ProcessTransaction;
use App\Jobs\TestJob;

class TransactionController extends Controller
{
    public function creerTransaction(Request $req, $id)
    { 
        $samemobileMoney=true;
        $samebanq=true;
        //recevoir taux de change via api
        $apiKey = env('OPEN_EXCHANGE_RATES_API_KEY'); 

            $client = new Client([
                'base_uri' => 'https://openexchangerates.org/api/',
            ]);

            // Specify base currency (EUR) and desired symbols (MGA)
            $response = $client->request('GET', 'latest.json', [
                'query' => [
                    'app_id' => $apiKey,
                    'symbols' => 'MGA',
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            response()->json([
                'success' => true,
                'data' => $data,
            ]);
    
        $rates=$data['rates'];

        $porteeTransaction=$req->porteeTransaction;
        $typeTransaction=$req->typeTransaction;
        $sommeTransaction=$req->sommeTransaction;
        $compteExpediteur=$req->compteExpediteur;
        $compteDestinataire=$req->compteDestinataire;
       

        if($porteeTransaction == 'local') {
            $tauxDeChange=0;
            if($typeTransaction == 'bankToMobileMoney') {
                $fraisTransfert = 1000; // frais en monnaie locale
                $delais = 60; // délai en minutes
            }
        
            elseif($typeTransaction == 'bankToBank') {
                if($samebanq) {
                    $fraisTransfert = 500; // frais réduits si même banque
                    $delais = 6; // délai en minutes
                } else {
                    $fraisTransfert = 1500; // frais pour différentes banques
                    $delais = 120;
                }
                // Handle bank-to-bank transaction using Stripe
                
                    // Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
                    // // Récupérer les détails du compte
                    // $account = \Stripe\Account::retrieve();

                    // try {
                    //     // Créer un compte connecté de test
                    //     $connectedAccount = Account::create([
                    //         'type' => 'custom',
                    //         'country' => 'US',
                    //         'email' => 'iloorandrianatoandro@gmail.com',
                    //         'business_type' => 'individual',
                    //     ]);
                    
                    //     echo "Connected account ID: " . $connectedAccount->id;
                    
                    // } catch (\Stripe\Exception\ApiErrorException $e) {
                    //     echo "Error: " . $e->getMessage();
                    // }

                    // try {
                    //     // Créer un transfert vers le compte connecté
                    //     $transfer = Transfer::create([
                    //         'amount' => 1000, // Montant en cents (10.00 USD)
                    //         'currency' => 'usd',
                    //         'destination' => $connectedAccount->id, // Remplacez par l'ID de votre compte connecté
                    //     ]);
                    
                    //     echo "Transfer ID: " . $transfer->id;
                    
                    // } catch (\Stripe\Exception\ApiErrorException $e) {
                    //     echo "Error: " . $e->getMessage();
                    // }
                    // try {
                    //     // Créer un payout en mode test
                    //     $payout = Payout::create([
                    //         'amount' => 500, // Montant en cents (5.00 USD)
                    //         'currency' => 'usd',
                    //         'destination' => 'ba_1Gq2PjFZHpbuWzcq43ufRNJX', // Remplacez par l'ID de votre compte bancaire de test
                    //         'description' => 'Test Payout',
                    //     ], [
                    //         'stripe_account' => $connectedAccount->id // Remplacez par l'ID de votre compte connecté
                    //     ]);
                    
                    //     echo "Payout ID: " . $payout->id;
                    
                    // } catch (\Stripe\Exception\ApiErrorException $e) {
                    //     echo "Error: " . $e->getMessage();
                    // }
                }
            
        
            elseif($typeTransaction == 'MobileMoneyToMobileMoney') {
                if($samemobileMoney) {
                    $fraisTransfert = 500; // frais réduits si même fournisseur mobile money
                    $delais = 5; // délai en minutes
                }
                else{
                    $fraisTransfert = 800; // frais pour transfert entre mobile money
                    $delais = 15; // délai en minutes
                }
            }
        
        } elseif($porteeTransaction == 'international') {
            $tauxDeChange=$rates['MGA'];
            if($typeTransaction == 'bankToMobileMoney') {
                $fraisTransfert = 5000; // frais pour transfert international bank-to-mobile money
                $delais = 24 * 60; // délai en minutes (24 heures)
            }
        
            elseif($typeTransaction == 'bankToBank') {
                $fraisTransfert = 4500; // frais pour transfert international bank-to-bank
                $delais = 3 * 24 * 60; // délai en minutes (3 jours ouvrables)
            }
        }
       
       
       
       
        //condition transaction somme compte+frais > somme transaction
        $sommeCompte = DB::table('comptes')
                ->where('user_id', $id)
                ->where('destinataire', false)
                ->where('numeroCompte', $compteExpediteur)
                ->value('somme');
        $paie=$sommeTransaction+$fraisTransfert;
        if($sommeCompte>=$paie){
            //creer transation
             // Créer la transaction
            $transaction = new Transaction;
            $transaction->tauxDeChange = $tauxDeChange;
            $transaction->porteeTransaction = $porteeTransaction;
            $transaction->typeTransaction = $typeTransaction;
            $transaction->fraisTransfert = $fraisTransfert;
            $transaction->delais = $delais;
            $transaction->compteExpediteur = $compteExpediteur;
            $transaction->compteDestinataire = $compteDestinataire;
            $transaction->sommeTransaction = $sommeTransaction;
            $transaction->dateEnvoi = Carbon::now();
            $transaction->user_id = $id;  
            $transaction->dateReception = Carbon::now()->addMinutes($delais);   
            $transaction->etatTransation = "en cours";   
            $transaction->save();
            //update somme expediteur & destinataire apres delais
            ProcessTransaction::dispatch(
                $tauxDeChange, 
                $porteeTransaction, 
                $typeTransaction, 
                $fraisTransfert, 
                $delais, 
                $compteExpediteur, 
                $compteDestinataire, 
                $sommeTransaction, 
                $id
            )->delay(now()->addSeconds($delais));

            return $transaction;
        }
       else{
        return "somme manquante";
       }
        
    }
    public function listerTransaction($id)
    {
        $transactions=DB::select("select * from transactions where user_id='$id'");
        return $transactions;
    }
    public function consulterTransaction($id)
    {
        $transaction = Transaction::find($id);
        return $transaction;
    }

  
}
