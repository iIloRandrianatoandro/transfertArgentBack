<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\user;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comptes', function (Blueprint $table) {
            $table->id();
            $table->string('typeCompte');
            $table->string('numeroCompte');
            $table->string('motDePasseCompte');
            $table->decimal('somme');
            $table->foreignIdFor(user::class)->default(1);
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('comptes');
    }
};
