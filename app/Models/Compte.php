<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory;
    protected $primaryKey = 'idCompte';
    protected $guarded=[];
    protected $fillable = ['typeCompte','numeroCompte','motDePasseCompte','somme'];
}
