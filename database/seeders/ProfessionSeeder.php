<?php

namespace Database\Seeders;

use App\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! config('templatecookie.testing_mode')) {
            $professions = [
                'Physician', 'Engineer', 'Chef', 'Lawyer', 'Designer', 'Labourer', 'Dentist', 'Accountant', 'Dental Hygienist', 'Actor', 'Electrician', 'Software Developer', 'Pharmacist', 'Technician', 'Artist', 'Teacher', 'Journalist', 'Cashier', 'Secretary', 'Scientist', 'Soldier', 'Gardener', 'Farmer', 'Librarian', 'Driver', 'Fishermen', 'Police Officer ', 'Tailor',
            ];
        } else {
            $professions = ['Physician', 'Engineer'];
        }

        // foreach ($professions as $data) {
        //     Profession::create([
        //         'name' => $data
        //     ]);
        // }

        $languages = loadLanguage();

        foreach ($professions as $data) {
            $translation = new Profession();
            $translation->save();

            foreach ($languages as $language) {
                $translation->translateOrNew($language->code)->name = $data;
            }

            $translation->save();
        }
    }
}
