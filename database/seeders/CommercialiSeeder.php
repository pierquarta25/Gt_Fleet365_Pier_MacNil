<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CommercialiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commerciali = [
            ['name' => 'Gabriella Costanza', 'email' => 'gabriella.costanza@macnil.it', 'slug' => 'gabriella-costanza'],
            ['name' => 'Miria Varvara', 'email' => 'miria.varvara@macnil.it', 'slug' => 'miria-varvara'],
            ['name' => 'Marco Defilippo', 'email' => 'marco.defilippo@macnil.it', 'slug' => 'marco-defilippo'],
            ['name' => 'Samuele Meliddo', 'email' => 'samuele.meliddo@macnil.it', 'slug' => 'samuele-meliddo'],
            ['name' => 'Daniele Gandini', 'email' => 'daniele.gandini@macnil.it', 'slug' => 'daniele-gandini'],
            ['name' => 'Beppe Vero', 'email' => 'giuseppe.vero@macnil.it', 'slug' => 'beppe-vero'],
        ];

        foreach ($commerciali as $c) {
            $user = User::firstOrNew(['email' => $c['email']]);
            $user->fill(['name' => $c['name'], 'slug' => $c['slug']]);
            if (!$user->exists) {
                // Non fanno login (usano solo il link): password casuale, mai resettata ai deploy successivi
                $user->password = Hash::make(Str::random(40));
            }
            $user->save();
        }
    }
}
