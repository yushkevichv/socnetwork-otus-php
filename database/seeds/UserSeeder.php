<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $password = \Illuminate\Support\Facades\Hash::make('password');
//        $users = factory(\App\User::class, 1000)->make()->toArray();
//
//        foreach (array_chunk($users, 200) as $chunkedUsers) {
//            \App\User::insert($chunkedUsers);
//        }

        for($i=0; $i<200; $i++) {
            for($j=0; $j<5000; $j++) {
                $users[] = [
                    'name' => $faker->name,
                    'last_name' => $faker->lastName,
                    'birthday' => $faker->dateTimeBetween('-50 years', '-18 years'),
                    'gender' => rand(1, 2),
                    'city' => $faker->city,
                    'interests' => $faker->word,
                    'email' => $faker->unique()->safeEmail,
                    'email_verified_at' => now(),
                    'password' => $password,
                    'remember_token' => \Illuminate\Support\Str::random(10),
                ];
            }
            try {
                \App\User::insert($users);
            }
            catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error($e->getMessage());
            }
            unset($users);
        }

//
//        for ($i=0; $i < 5; $i++) {
//            factory(\App\User::class, 200)->create();
//        }
    }
}
