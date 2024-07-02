<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use DB;

class TransactionController extends Controller
{
    public function creerTransaction(Request $req, $id)
    { $samemobileMoney=true;
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
       
        if($porteeTransaction == 'local') {
            $tauxDeChange=0;
            if($typeTransaction == 'bankToMobileMoney') {
                $fraisTransfert = 1000; // frais en monnaie locale
                $delais = 60; // délai en minutes
            }
        
            elseif($typeTransaction == 'bankToBank') {
                if($samebanq) {
                    $fraisTransfert = 500; // frais réduits si même banque
                    $delais = 30; // délai en minutes
                } else {
                    $fraisTransfert = 1500; // frais pour différentes banques
                    $delais = 120;
                }
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
       
        // creer transaction
        $transaction= new Transaction;
        $transaction->tauxDeChange=$tauxDeChange;
        $transaction->porteeTransaction=$porteeTransaction;
        $transaction->typeTransaction=$typeTransaction;
        $transaction->fraisTransfert=$fraisTransfert;
        $transaction->delais=$delais;
        $sommeTransaction=$req->sommeTransaction;
        $transaction->sommeTransaction=$sommeTransaction;
        $compteExpediteur=$req->compteExpediteur;
        $compteDestinataire=$req->compteDestinataire;
        $transaction->compteExpediteur=$compteExpediteur;
        $transaction->compteDestinataire=$compteDestinataire;
        $transaction->dateEnvoi=Carbon::now();
        $transaction->user_id=$id;  
        $transaction->dateReception = Carbon::now()->addMinutes($delais);   
        $transaction->etatTransation="en cours";   
        $transaction->save();
       
        //condition transaction somme compte+frais > somme transaction
        //compte destinataire +somme
        //DB::select("update comptes set somme=somme+'$sommeTransaction' where user_id='$id' and destinataire=false and numeroCompte='$compteDestinataire'");
        DB::update("UPDATE comptes SET somme = somme + ? WHERE user_id = ? AND destinataire = ? AND numeroCompte = ?", [
            $sommeTransaction,
            $id,
            true,
            $compteDestinataire
        ]);
        
        //compte expediteur -somme -frais
        //DB::select("update comptes set somme=somme-'$sommeTransaction' where user_id='$id' and destinataire=true and numeroCompte='$compteExpediteur'");
        DB::update("UPDATE comptes SET somme = somme - ? - ? WHERE user_id = ? AND destinataire = ? AND numeroCompte = ?", [
            $sommeTransaction,
            $fraisTransfert,
            $id,
            false,
            $compteExpediteur
        ]);
        
        return $transaction;
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
