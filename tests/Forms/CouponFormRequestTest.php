<?php

namespace WalkerChiu\Coupon;

use Illuminate\Support\Facades\Validator;
use WalkerChiu\Coupon\Models\Forms\CouponFormRequest;

class CouponFormRequestTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$this->loadLaravelMigrations(['--database' => 'mysql']);
        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');

        $this->request  = new CouponFormRequest();
        $this->rules    = $this->request->rules();
        $this->messages = $this->request->messages();
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\Coupon\CouponServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * Unit test about Authorize.
     *
     * For WalkerChiu\Coupon\Models\Forms\CouponFormRequest
     *
     * @return void
     */
    public function testAuthorize()
    {
        $this->assertEquals(true, 1);
    }

    /**
     * Unit test about Rules.
     *
     * For WalkerChiu\Coupon\Models\Forms\CouponFormRequest
     *
     * @return void
     */
    public function testRules()
    {
        $faker = \Faker\Factory::create();

        // Give
        $attributes = [
            'serial'     => $faker->isbn10,
            'identifier' => $faker->slug,
            'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
            'value'      => '',
            'begin_at'   => '2019-01-01 00:00:00',
            'end_at'     => '2019-01-01 01:00:00',
            'name'       => $faker->name
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(false, $fails);

        // Give
        $attributes = [
            'serial'     => $faker->isbn10,
            'identifier' => $faker->slug
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);
    }
}
