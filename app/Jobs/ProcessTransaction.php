<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;
use Carbon\Carbon;
use DB;
use App\Models\User;
use App\Notifications\TransactionComplete;

class ProcessTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $idTransaction;
    protected $tauxDeChange;
    protected $porteeTransaction;
    protected $typeTransaction;
    protected $fraisTransfert;
    protected $delais;
    protected $compteExpediteur;
    protected $compteDestinataire;
    protected $sommeTransaction;
    protected $id;

    public function __construct($idTransaction, $tauxDeChange, $porteeTransaction, $typeTransaction, $fraisTransfert, $delais, $compteExpediteur, $compteDestinataire, $sommeTransaction, $id)
    {
        $this->idTransaction = $idTransaction;
        $this->tauxDeChange = $tauxDeChange;
        $this->porteeTransaction = $porteeTransaction;
        $this->typeTransaction = $typeTransaction;
        $this->fraisTransfert = $fraisTransfert;
        $this->delais = $delais;
        $this->compteExpediteur = $compteExpediteur;
        $this->compteDestinataire = $compteDestinataire;
        $this->sommeTransaction = $sommeTransaction;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
  public function handle()
{
    // Récupérer la transaction
    $transaction = Transaction::find($this->idTransaction);
    DB::update("UPDATE transactions SET etatTransation = ? WHERE id = ? ", [
        "achevee",
        $this->idTransaction
    ]);

    // Mettre à jour les comptes
    DB::update("UPDATE comptes SET somme = somme + ? WHERE user_id = ? AND destinataire = ? AND numeroCompte = ?", [
        $this->sommeTransaction,
        $this->id,
        true,
        $this->compteDestinataire
    ]);

    DB::update("UPDATE comptes SET somme = somme - ? - ? WHERE user_id = ? AND destinataire = ? AND numeroCompte = ?", [
        $this->sommeTransaction,
        $this->fraisTransfert,
        $this->id,
        false,
        $this->compteExpediteur
    ]);

    // // Envoyer la notification
    $user = User::find($this->id);
    $user->notify(new TransactionComplete($transaction));
}


}
