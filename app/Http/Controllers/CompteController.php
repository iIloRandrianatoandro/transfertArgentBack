<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;
use Hash;
use DB;

class CompteController extends Controller
{
   
    public function associerCompte(Request $req, $userId)
    {
        $compte= new Compte;
        $compte->typeCompte=$req->typeCompte;
        $compte->nomCompte=$req->nomCompte;
        $compte->numeroCompte=$req->numeroCompte;
        $compte->somme=$req->somme;
        $compte->destinataire=$req->destinataire;
        $compte->motDePasseCompte=Hash::make($req->motDePasseCompte);
        $compte->user_id=$userId;        
        $compte->save();
        return $compte;
    }
    public function listerCompteExpediteur($id)
    { 
        $comptes=DB::select("select * from comptes where user_id='$id' and destinataire=false");
        return $comptes;
    }
    public function listerCompteDestinataire($id)
    { 
        $comptes=DB::select("select * from comptes where user_id='$id' and destinataire=true ");
        return $comptes;
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
