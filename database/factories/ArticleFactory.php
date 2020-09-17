<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Article::class, function (Faker $faker) {
    return [
        'title' => 'title-' . rand(1,1000),
        'description' => 'description-' . rand(1,1000),
        'created_at' => now(),
        'updated_at' => now(),
        'content' =>'content-' . rand(1,1000),
        'tag' => 'lpr,111'
    ];
});
