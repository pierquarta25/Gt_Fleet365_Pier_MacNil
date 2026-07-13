<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommercialiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commerciali = [
            ['name' => 'Agente 1', 'email' => 'agente1@macnil.it', 'slug' => 'agente-1'],
            ['name' => 'Agente 2', 'email' => 'agente2@macnil.it', 'slug' => 'agente-2'],
            ['name' => 'Agente 3', 'email' => 'agente3@macnil.it', 'slug' => 'agente-3'],
            ['name' => 'Agente 4', 'email' => 'agente4@macnil.it', 'slug' => 'agente-4'],
            ['name' => 'Agente 5', 'email' => 'agente5@macnil.it', 'slug' => 'agente-5'],
            ['name' => 'Commerciale 1', 'email' => 'commerciale1@macnil.it', 'slug' => 'commerciale-1'],
            ['name' => 'Commerciale 2', 'email' => 'commerciale2@macnil.it', 'slug' => 'commerciale-2'],
            ['name' => 'Commerciale 3', 'email' => 'commerciale3@macnil.it', 'slug' => 'commerciale-3'],
            ['name' => 'Commerciale 4', 'email' => 'commerciale4@macnil.it', 'slug' => 'commerciale-4'],
            ['name' => 'Commerciale 5', 'email' => 'commerciale5@macnil.it', 'slug' => 'commerciale-5'],
            ['name' => 'Commerciale 6', 'email' => 'commerciale6@macnil.it', 'slug' => 'commerciale-6'],
            ['name' => 'Test User', 'email' => 'test@example.com', 'slug' => 'test-user'],
            ['name' => 'Mario Rossi', 'email' => 'mario.rossi@esempio.it', 'slug' => 'mario-rossi'],
            ['name' => 'Luca Bianchi', 'email' => 'luca.bianchi@esempio.it', 'slug' => 'luca-bianchi'],
            ['name' => 'Anna Verdi', 'email' => 'anna.verdi@esempio.it', 'slug' => 'anna-verdi'],
            ['name' => 'Giuseppe Neri', 'email' => 'giuseppe.neri@esempio.it', 'slug' => 'giuseppe-neri'],
            ['name' => 'Elena Blu', 'email' => 'elena.blu@esempio.it', 'slug' => 'elena-blu'],
            ['name' => 'Pierfilippo Quartarella', 'email' => 'info@getpierfilippo.com', 'slug' => 'pier-quartarella'],
            ['name' => 'Gabriella Costanza', 'email' => 'gabriella.costanza@macnil.it', 'slug' => 'gabriella-costanza'],
            ['name' => 'Miria Varvara', 'email' => 'miria.varvara@macnil.it', 'slug' => 'miria-varvara'],
            ['name' => 'Marco Defilippo', 'email' => 'marco.defilippo@macnil.it', 'slug' => 'marco-defilippo'],
            ['name' => 'Samuele Meliddo', 'email' => 'samuele.meliddo@macnil.it', 'slug' => 'samuele-meliddo'],
            ['name' => 'Daniele Gandini', 'email' => 'daniele.gandini@macnil.it', 'slug' => 'daniele-gandini'],
            ['name' => 'Beppe Vero', 'email' => 'giuseppe.vero@macnil.it', 'slug' => 'beppe-vero'],
        ];

        foreach ($commerciali as $c) {
            User::updateOrCreate(
                ['email' => $c['email']], // Condizione di unicità
                [
                    'name' => $c['name'],
                    'slug' => $c['slug'],
                    // Password fittizia (non devono per forza fare login se usano solo il link)
                    'password' => Hash::make('password_temporanea_123!')
                ]
            );
        }
    }
}
