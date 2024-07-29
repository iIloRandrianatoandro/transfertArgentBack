<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Compte extends Model
{
    use HasFactory, Billable;
    protected $primaryKey = 'id';
    protected $guarded=[];
    protected $fillable = ['typeCompte','numeroCompte','motDePasseCompte','somme','user_id','nomCompte','stripe_account_id','adresse'];
}
