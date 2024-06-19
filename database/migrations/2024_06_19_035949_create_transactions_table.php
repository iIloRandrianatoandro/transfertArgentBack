<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\user;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('compteExpediteur');
            $table->string('compteDestinataire');
            $table->string('typeTransaction');
            $table->string('typeCompteDestinataire');
            $table->string('instutitionFinanciereDestinataire');
            $table->decimal('sommeTransaction');
            $table->date('dateEnvoi');
            $table->date('dateReception');
            $table->decimal('fraisTransfert');
            $table->decimal('tauxDeChange');
            $table->string('delais');
            $table->string('etatTransation');
            $table->foreignIdFor(user::class)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
