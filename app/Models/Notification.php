<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;    
    protected $primaryKey = 'idCompte';
    protected $guarded=[];
    protected $fillable = ['message','lu','dateEnvoiNotification','typeMessage','canalEnvoi'];
}
