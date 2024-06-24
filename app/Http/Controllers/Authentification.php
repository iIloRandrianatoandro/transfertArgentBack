<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;
use Session;
use Carbon\Carbon;
use Socialite;
use Str;
use DB;

class Authentification extends Controller
{
    public function redirectToGoogle() //redirection vers la page de connection de google
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback() //recuperer le compte google et se connecter via le compte googlle
    {
        try {
            $user = Socialite::driver('google')->stateless()->user(); //recuperer le compte google
            $findUser = User::where('google_id', $user->id)->first();
            if ($findUser) { //si oui se connecter
                Auth::login($findUser);
                // $token = $findUser->createToken('Personal Access Token')->accessToken;
                // return response()->json(['token' => $token], 200);
                return ['message'=>'connecte','user'=>$findUser];
            } else { //sinon creer le compte puis se connecter
                $newUser= new User;
                $newUser->email=$user->email;
                $newUser->google_id=$user->id;
                $newUser->password=encrypt(Str::random(16));
                $newUser->save();
                Auth::login($newUser);
                // $token = $newUser->createToken('Personal Access Token')->accessToken;
                // return response()->json(['token' => $token], 200);
                return ['message'=>'connecte, utilisateur créé','user'=>$newUser];
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    public function sInscrire(Request $req){ //nouvel utilisateur
        $User= new User;
        $User->email=$req->email;
        $User->password=Hash::make($req->password);
        $User->save();
        return response(['message' => 'Inscription effectuée avec succès', 'user' => $User]);
    }
    public function ajouterInformation(Request $req, $id)
    {
        $user = User::find($id);
            
        // Mettez à jour les champs de l'utilisateur
        $user->update([
            'nom' => $req->nom,
            'prenom'=> $req->prenom,
            'adresse'=> $req->adresse,
            'telephone' => $req->telephone,
            'dateNais'=> $req->dateNais,
        ])
        ;
        return $user;
    }
    public function seConnecter(Request $req){ //login
       if(Auth()->attempt($req->only('email', 'password'))){
        return Auth::user()->id;
       }
       else{
        return 'erreur authentification';
       } 
    }
    public function verifierCode(Request $req,$code){
        if($req->codeEntre != $code){
            return 'code incorrect';
        }
        else {
            return 'code correct';
        }
    }
    public function envoyerCodeSmsVerificationTelephone(Request $req){
        $message=Str::random(4);
        $numero=$req->numero;
        
        $basic  = new \Vonage\Client\Credentials\Basic("b9e281c4", "0vNbn9MnOIBIx79x");
        $client = new \Vonage\Client($basic);

        // Set the CA bundle path for Guzzle with a relative path
        $guzzleClient = new \GuzzleHttp\Client([
        'verify' => storage_path('cacert.pem'),
        ]);
        $client->setHttpClient($guzzleClient); //for more seurity

        //envoi message
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($numero, "Transfert d'argent Ilo", "Application de transfert d argent Ilo '\n' Voici votre code de vérification '$message'")
        );

        $message = $response->current();

        if ($message->getStatus() == 0) {
           return ["message"=>"Message envoyé\n","code"=>$message];
        } else {
            return "Message non evoyé " . $message->getStatus() . "\n";
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
