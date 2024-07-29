<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;
use Hash;
use DB;
use Stripe\Stripe;


class CompteController extends Controller
{
   
    public function associerCompte(Request $req, $userId)
    {
        
        $compte= new Compte;
        $compte->typeCompte=$req->typeCompte; //bancaire oe mobile money
        $compte->nomCompte=$req->nomCompte; //nom banque ou mobile money
        $compte->numeroCompte=$req->numeroCompte;
        $compte->somme=$req->somme;
        $destinataire=false;
        if($req->destinataire=="true"){
            $destinataire=true;
        }
        $compte->destinataire=$destinataire;
        $compte->motDePasseCompte=Hash::make($req->motDePasseCompte);
        $compte->user_id=$userId; 
         
        //associer le compte à un compte stripe si le typeCompte compte bancaire
        // $typeCompte = $req->typeCompte;
        // if ($typeCompte == 'compte bancaire') {
        //     try {
        //         // Set Stripe API key
        //         Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    
        //         // Create a Stripe customer using the user's email
        //         $customer = \Stripe\Customer::create([
        //             //'email' => auth()->user()->email,
        //             'email' => 'iloorandrianatoandro@gmail.com',
        //         ]);
    
        //         // Create a Stripe bank account token using the provided bank account details
        //         $token = \Stripe\Token::create([
        //             'bank_account' => [
        //                 'country' => 'US', // Replace with your country code
        //                 'currency' => 'usd', // Bank account currency
        //                 'account_holder_name' => 'i Ilo', // Replace with actual account holder name
        //                 'account_holder_type' => 'individual', // Replace with 'individual' or 'company'
        //                 'routing_number' => '110000000', // Test routing number
        //                 'account_number' => '000123456789', // Test account number
        //                 'usage' => 'source' // Indicate that this is a payment source
        //             ],
        //         ]);
            
        //         // Attach the bank account to the customer
        //         \Stripe\Customer::createSource(
        //             $customer->id,
        //             ['source' => $token->id]
        //         );
            
        //         // Store the Stripe customer ID in the `Compte` model
        //         $compte->stripe_account_id = $customer->id;
    
        //     } catch (\Stripe\Exception $e) {
        //         return response()->json(['error' => $e->getMessage()], 500);
        //     }
        // }
        
        $compte->save();
        return $compte;
    }
    public function listerCompteExpediteur($id)
    { 
        $comptes=DB::select("select * from comptes where user_id='$id' and destinataire=false");
        return $comptes;
    }
    public function listerCompteDestinataire($id, )
    { 
        $comptes=DB::select("select * from comptes where user_id='$id' and destinataire=true ");
        return $comptes;
    }
    public function listerCompteExpediteurSelonTypeCompte(Request $req, $id)
    { 
        $typeCompte=$req->typeCompte;
        $comptes=DB::select("select * from comptes where user_id='$id' and destinataire=false and typeCompte ='$typeCompte'");
        return $comptes;
    }
    public function listerCompteDestinataireSelonTypeCompte(Request $req, $id)
    { 
        $typeCompte=$req->typeCompte;
        $comptes=DB::select("select * from comptes where user_id='$id' and destinataire=true and typeCompte ='$typeCompte'");
        //return $comptes;
        return response()->json($comptes);
    }

    public function consulterCompte($id)
    {
        $compte = Compte::find($id);
        return $compte;
    }

    public function supprimerCompte($id)
    {
        $compte = Compte::find($id);    
        $compte->delete();
    
        return "Compte supprimé avec succès";
    }

}
