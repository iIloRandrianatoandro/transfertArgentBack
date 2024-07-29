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
use App\Models\User;
use App\Notifications\TransactionCree;


class TransactionController extends Controller
{
    public function voirCoutTransaction(Request $req)
    {
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
        if ($req->madaToUs=='true'){
            $madaToUs=true;
        }
        else if ($req->madaToUs=='false'){
            $madaToUs=true;
        }
        $samemobileMoney=true;
        $samebanq=true;

        if($porteeTransaction == 'local') {
            $tauxDeChange=0;
            if($typeTransaction == 'Bank To Mobile Money') {
                $fraisTransfert = 1000; // frais en monnaie locale
                $delais = 6; // délai en secondes
            }
        
            elseif($typeTransaction == 'Bank To Bank') {
                if($samebanq) {
                    $fraisTransfert = 500; // frais réduits si même banque
                    $delais = 3; // délai en minutes
                } else {
                    $fraisTransfert = 1500; // frais pour différentes banques
                    $delais = 5;
                }
                
            }          
        
            elseif($typeTransaction == 'Mobile Money To Mobile Money') {
                if($samemobileMoney) {
                    $fraisTransfert = 500; // frais réduits si même fournisseur mobile money
                    $delais = 0; // délai en minutes
                }
                else{
                    $fraisTransfert = 800; // frais pour transfert entre mobile money
                    $delais = 5; // délai en minutes
                }
            }
        
        } elseif($porteeTransaction == 'international') {
            $tauxDeChange=$rates['MGA'];
            //mada to us ou us to mada miova sommeTransaction dia misy conversion kely
            if($madaToUs){
                $sommeTransaction = $sommeTransaction*$tauxDeChange;
            }
            else{
                $sommeTransaction = $sommeTransaction/$tauxDeChange;
            }
            if($typeTransaction == 'Bank To Mobile Money') {
                $fraisTransfert = 500; // frais pour transfert international bank-to-mobile money
                $delais = 24 * 60; // délai en minutes (24 heures)
            }
        
            elseif($typeTransaction == 'Bank To Bank') {
                $fraisTransfert = 450; // frais pour transfert international bank-to-bank
                $delais = 3 * 24 * 60; // délai en minutes (3 jours ouvrables)
            }
        }
        return ["compteExpediteur"=>$compteExpediteur,"compteDestinataire"=>$compteDestinataire,"delais"=>$delais,"typeTransaction"=>$typeTransaction,"fraisTransfert"=>$fraisTransfert,"porteeTransaction"=>$porteeTransaction,"tauxDeChange"=>$tauxDeChange,"sommeTransaction"=>$sommeTransaction,];

    }
    public function creerTransaction(Request $req, $id)
    { 

        $porteeTransaction=$req->porteeTransaction;
        $typeTransaction=$req->typeTransaction;
        $sommeTransaction=$req->sommeTransaction;
        $compteExpediteur=$req->compteExpediteur;
        $compteDestinataire=$req->compteDestinataire;
        $fraisTransfert=$req->fraisTransfert;
        $tauxDeChange=$req->tauxDeChange;
        $delais=$req->delais;       
            
        //condition transaction somme compte+frais > somme transaction
        $sommeCompte = DB::table('comptes')
                ->where('user_id', $id)
                ->where('destinataire', false)
                ->where('numeroCompte', $compteExpediteur)
                ->value('somme');
        $paie=$sommeTransaction+$fraisTransfert;
        if($sommeCompte>=$paie){
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
            // Envoyer notification à l'utilisateur
            $user = User::find($id); // Récupérer l'utilisateur
            $user->notify(new TransactionCree($transaction));
            //update somme expediteur & destinataire apres delais
            $idTransaction=$transaction->id ;
            //return $idTransaction;
            // Mettre à jour les comptes        
            ProcessTransaction::dispatch(
                $idTransaction,
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
