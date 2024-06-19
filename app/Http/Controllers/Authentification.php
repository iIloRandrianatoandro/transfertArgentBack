<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;
use Session;
use Carbon\Carbon;

class Authentification extends Controller
{
    public function sInscrire(Request $req){//nouvel utilisateur
        $User= new User;
        $User->nom=$req->nom;
        $User->email=$req->email;
        $User->prenom=$req->prenom;
        $User->adresse=$req->adresse;
        $User->telephone=$req->telephone;
        $User->dateNais=$req->dateNais;
        //$User->dateNais = Carbon::parse($req->dateNais)->format('Y-m-d'); 
        $User->password=Hash::make($req->password);
        $User->save();
        return response(['message' => 'Inscription effectuée avec succès', 'user' => $User]);
    }
    public function seConnecter(Request $req){ //login
       if(Auth()->attempt($req->only('email', 'password'))){
        return Auth::user()->id;
       }
       else{
        return 'erreur autnetification';
       } 
    }
    public function seDeconnecter(){ //logout
        Auth::logout();
        return 'deconnecte';
    }
    public function modifierProfil(Request $req, $id)
    {
        $user = User::find($id);
            
        // Mettez à jour les champs de l'utilisateur
        $user->update([
            'nom' => $req->nom,
            'email' => $req->email,
            'prenom'=> $req->prenom,
            'adresse'=> $req->adresse,
            'telephone' => $req->telephone,
            'dateNais'=> $req->dateNais,
            'password'=> bcrypt($req->password),
        ])
        ;
        return $user;
    }
    public function supprimerProfil($id)
    {
        $user = User::find($id);    
        $user->delete();
    
        return "Utilisateur supprimé avec succès";
    }
    public function consulterProfil($id)
    {
        $user = User::find($id);
    
        return $user;
    }
}
