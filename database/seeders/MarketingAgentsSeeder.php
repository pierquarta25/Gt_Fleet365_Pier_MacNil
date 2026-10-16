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
        // Link personalizzati (da condividere con ogni commerciale):
        // Gabriella Costanza  → https://smartfleet-configurator.onrender.com/c/gabriella-costanza
        // Miria Varvara       → https://smartfleet-configurator.onrender.com/c/miria-varvara
        // Marco Defilippo     → https://smartfleet-configurator.onrender.com/c/marco-defilippo
        // Samuele Meliddo     → https://smartfleet-configurator.onrender.com/c/samuele-meliddo
        // Daniele Gandini     → https://smartfleet-configurator.onrender.com/c/daniele-gandini
        // Beppe Vero          → https://smartfleet-configurator.onrender.com/c/beppe-vero

        $commerciali = [
            [
                'name' => 'Gabriella Costanza',
                'email' => 'gabriella.costanza@macnil.it',
                'slug' => 'gabriella-costanza', // /c/gabriella-costanza
            ],
            [
                'name' => 'Miria Varvara',
                'email' => 'miria.varvara@macnil.it',
                'slug' => 'miria-varvara', // /c/miria-varvara
            ],
            [
                'name' => 'Marco Defilippo',
                'email' => 'marco.defilippo@macnil.it',
                'slug' => 'marco-defilippo', // /c/marco-defilippo
            ],
            [
                'name' => 'Samuele Meliddo',
                'email' => 'samuele.meliddo@macnil.it',
                'slug' => 'samuele-meliddo', // /c/samuele-meliddo
            ],
            [
                'name' => 'Daniele Gandini',
                'email' => 'daniele.gandini@macnil.it',
                'slug' => 'daniele-gandini', // /c/daniele-gandini
            ],
            [
                'name' => 'Beppe Vero',
                'email' => 'giuseppe.vero@macnil.it',
                'slug' => 'beppe-vero', // /c/beppe-vero
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
