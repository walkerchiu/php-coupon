<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\Coupon\Models\Entities\Coupon;
use WalkerChiu\Coupon\Models\Entities\CouponLang;

$factory->define(Coupon::class, function (Faker $faker) {
    return [
        'serial'     => $faker->isbn10,
        'identifier' => $faker->slug,
        'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
        'value'      => '',
        'begin_at'   => '2019-01-01 00:00:00',
        'end_at'     => '2019-01-01 01:00:00'
    ];
});

$factory->define(CouponLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
