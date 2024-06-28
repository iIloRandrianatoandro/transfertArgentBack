<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    
    // public function test(Request $req)
    // {
    //     // Set your Stripe API key.
    // \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    // $montant=$req->montant;
    // // Create a new Stripe charge.
    // $charge = \Stripe\Charge::create([
    //     'amount' => $montant,
    //     'currency' => 'mgm',
    //     'customer' => 1,
    // ]);

    // // Display a success message to the user.
    // return 'Payment successful!';

    // }
    public function creerTransaction(Request $request)
    {
        return "creerTransaction";
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
