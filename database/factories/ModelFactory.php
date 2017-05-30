<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'username' => $faker->unique()->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

/**
 * Copy the following artisan command to create 10 entries
 * 
 * factory(App\Channel::class, 10)->create()
 */
$factory->define(App\Channel::class, function (Faker\Generator $faker) {
	$word = $faker->unique()->word;
    return [
        'name' => $word,
        'slug' => str_slug($word, "-"),
        'description' => $faker->text,
        'color' => $faker->unique()->hexcolor,
    ];
});

/**
 * Copy the following artisan command to create 100 entries
 * 
 * factory(App\Discussion::class,100)->create()
 */
$factory->define(App\Discussion::class, function (Faker\Generator $faker) {
	$sentence = $faker->unique()->sentence;
    return [
        'user_id' => $faker->numberBetween(1, 20),
        'channel_id' => $faker->numberBetween(1, 10),
        'title' => $sentence,
        'slug' => str_slug($sentence, "-"),
        'body' => $faker->text,
        'is_approved' => true,
    ];
});

/**
 * Copy the following artisan command to create 100 entries
 * 
 * factory(App\Comment::class,100)->create()
 */
$factory->define(App\Comment::class, function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 20),
        'discussion_id' => $faker->numberBetween(1, 10),
        'title' => $faker->sentence,
        'content' => $faker->text,
        'approved' => true,
    ];
});
