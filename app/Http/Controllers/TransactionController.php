<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    
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
