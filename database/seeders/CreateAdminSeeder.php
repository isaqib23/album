<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name'=>'Admin',
                'email'=>'mfbx8ne5@gmail.com',
                'type'=>'admin',
                'status'=>'active',
                'password'=> bcrypt('Ackun131089'),
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
