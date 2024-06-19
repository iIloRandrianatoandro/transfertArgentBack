<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;

class CompteController extends Controller
{
   
    public function associerCompte(Request $request)
    {
        return "associerCompte";
    }

    public function modifierCompte(Request $request)
    {
        return "modifierCompte";
    }

    public function consulterCompte(Compte $compte)
    {
        return "consulterCompte";
    }
}
