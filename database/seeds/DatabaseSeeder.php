<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 20)->create();
        factory(App\Channel::class, 10)->create();
        factory(App\Discussion::class,100)->create();
        factory(App\Comment::class,100)->create();
    }
}
