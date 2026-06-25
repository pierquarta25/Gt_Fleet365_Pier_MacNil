<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('token', 64)->unique();
            $table->string('vehicle_type');        // id del mezzo (es. "furgone_frigo")
            $table->string('vehicle_name');         // nome leggibile (es. "FURGONE FRIGO")
            $table->integer('vehicle_qty');          // quantità dal lead
            $table->string('vehicle_img')->nullable();
            $table->json('client_data')->nullable(); // snapshot dati cliente
            $table->json('services')->nullable();    // servizi selezionati
            $table->text('notes')->nullable();
            $table->string('agent_email')->nullable();
            $table->string('hubspot_deal_id')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
