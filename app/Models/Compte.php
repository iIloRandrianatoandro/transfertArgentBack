<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $guarded=[];
    protected $fillable = ['typeCompte','numeroCompte','motDePasseCompte','somme','user_id'];
}
