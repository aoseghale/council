<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        factory(User::class)
            ->create([
                'name' => 'JohnDoe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password'),
            ]);

        create(User::class, [
            'name' => 'JaneDoe',
            'email' => 'jane.doe@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
