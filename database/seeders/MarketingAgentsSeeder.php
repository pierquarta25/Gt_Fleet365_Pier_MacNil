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
                'email' => 'info@getpierfilippo.com',
                'slug' => 'pier-quartarella'
            ],
            [
                'name' => 'Gabriella Costanza',
                'email' => 'gabriella.costanza@macnil.it',
                'slug' => 'gabriella-costanza'
            ],
            [
                'name' => 'Miria Varvara',
                'email' => 'miria.varvara@macnil.it',
                'slug' => 'miria-varvara'
            ],
            [
                'name' => 'Marco Defilippo',
                'email' => 'marco.defilippo@macnil.it',
                'slug' => 'marco-defilippo'
            ],
            [
                'name' => 'Samuele Meliddo',
                'email' => 'samuele.meliddo@macnil.it',
                'slug' => 'samuele-meliddo'
            ],
            [
                'name' => 'Daniele Gandini',
                'email' => 'daniele.gandini@macnil.it',
                'slug' => 'daniele-gandini'
            ],
            [
                'name' => 'Beppe Vero',
                'email' => 'giuseppe.vero@macnil.it',
                'slug' => 'beppe-vero'
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
