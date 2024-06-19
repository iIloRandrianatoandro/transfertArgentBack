<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $guarded=[];
    protected $fillable = ['compteExpaditeur','compteDestinataire','typeTransaction','typeCompteDestinataire','instutitionFinanciereDestinataire','sommeTransaction','dateEnvoi','dateReception','fraisTransfert','tauxDeChange','delais','etatTransation'];
}
