<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketingAgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commerciali = [
            [
                'name' => 'Pierfilippo Quartarella',
                'email' => 'ingo@getpierfilippo.com',
                'slug' => 'pier-quartarella'
            ],
            [
                'name' => 'Luca Bianchi',
                'email' => 'luca.bianchi@esempio.it',
                'slug' => 'luca-bianchi'
            ],
            [
                'name' => 'Anna Verdi',
                'email' => 'anna.verdi@esempio.it',
                'slug' => 'anna-verdi'
            ],
            [
                'name' => 'Giuseppe Neri',
                'email' => 'giuseppe.neri@esempio.it',
                'slug' => 'giuseppe-neri'
            ],
            [
                'name' => 'Elena Blu',
                'email' => 'elena.blu@esempio.it',
                'slug' => 'elena-blu'
            ],
            [
                'name' => 'Commerciale 6',
                'email' => 'commerciale6@macnil.it',
                'slug' => 'commerciale-6'
            ],
        ];

        foreach ($commerciali as $persona) {
            User::updateOrCreate(
                ['email' => $persona['email']],
                [
                    'name' => $persona['name'],
                    'slug' => $persona['slug'],
                    'password' => Hash::make('password_segreta'),
                    'role' => 'agent',
                ]
            );
        }
    }
}
