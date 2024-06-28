<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;

class TransactionController extends Controller
{
    public function creerTransaction(Request $req)
    {
        // $porteeTransaction=$req->porteeTransaction;
        // $typeTransaction=$req->typeTransaction;
        // $sommeTransaction=$req->sommeTransaction;
        // $compteExpediteur=$req->compteExpediteur;
        // $compteDestinataire=$req->compteDestinataire;
        // $fraisTransfert;
        // $tauxDeChange;
        // $delais;
        $apiKey = env('OPEN_EXCHANGE_RATES_API_KEY'); // Replace with your API key

        try {
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
    return response()->json([
        'success' => true,
        'data' => $data,
    ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    
    }
    public function listerTransaction(Request $request)
    {
        return "listerTransaction";
    }
    public function consulterTransaction(Request $request, $id)
    {
        return "consulterTransaction";
    }

  
}
