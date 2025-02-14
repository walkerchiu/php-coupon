<?php

namespace WalkerChiu\Coupon;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\Coupon\Models\Repositories\CouponRepository;
use WalkerChiu\Coupon\Models\Services\CouponService;

class CouponServiceTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected $repository;
    protected $service;

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

        $this->repository = $this->app->make(CouponRepository::class);
        $this->service = $this->app->make(CouponService::class);
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
     * Test checkTimeliness.
     *
     * For WalkerChiu\Coupon\Models\Services\CouponService
     *
     * @return void
     */
    public function testCheckTimeliness()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-coupon.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-coupon.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-coupon.soft_delete', 1);

        // Give
        $now = Carbon::now()->setTimezone('Asia/Taipei');

        $faker = \Faker\Factory::create();

        // begin_at & end_at
            // When
                $db_morph_1 = $this->repository->save([
                    'serial'     => $faker->isbn10,
                    'identifier' => $faker->slug,
                    'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                    'value'      => '',
                    'begin_at'   => '2020-01-01 00:00:00',
                    'end_at'     => '2025-01-01 01:00:00'
                ]);
            // Then
                $this->assertTrue( $this->service->checkTimeliness($db_morph_1->id, $now) );

            // When
                $db_morph_2 = $this->repository->save([
                    'serial'     => $faker->isbn10,
                    'identifier' => $faker->slug,
                    'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                    'value'      => '',
                    'begin_at'   => '2020-01-01 00:00:00',
                    'end_at'     => '2020-01-01 01:00:00'
                ]);
            // Then
                $this->assertTrue( ! $this->service->checkTimeliness($db_morph_2->id, $now) );

        // only_dayType
            // When
                $db_morph_3 = $this->repository->save([
                    'serial'     => $faker->isbn10,
                    'identifier' => $faker->slug,
                    'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                    'value'      => '',
                    'begin_at'   => '2020-01-01 00:00:00',
                    'end_at'     => '2025-01-01 01:00:00',
                    'only_dayType' => [1, 2, 3, 4]
                ]);
            // Then
                $this->assertTrue( $this->service->checkTimeliness($db_morph_3->id, $now) );

        // exclude_date
            // When
                $db_morph_4 = $this->repository->save([
                    'serial'     => $faker->isbn10,
                    'identifier' => $faker->slug,
                    'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                    'value'      => '',
                    'begin_at'   => '2020-01-01 00:00:00',
                    'end_at'     => '2025-01-01 01:00:00',
                    'exclude_date' => ['2020-06-01']
                ]);
            // Then
                $this->assertTrue( $this->service->checkTimeliness($db_morph_4->id, $now) );

        // exclude_time
            // When
                $db_morph_5 = $this->repository->save([
                    'serial'     => $faker->isbn10,
                    'identifier' => $faker->slug,
                    'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                    'value'      => '',
                    'begin_at'   => '2020-01-01 00:00:00',
                    'end_at'     => '2025-01-01 01:00:00',
                    'exclude_time' => ['00:00:00-05:00:00']
                ]);
            // Then
                $this->assertTrue( $this->service->checkTimeliness($db_morph_5->id, $now) );
    }

    /**
     * Test checkAvailability.
     *
     * For WalkerChiu\Coupon\Models\Services\CouponService
     *
     * @return void
     */
    public function testCheckAvailability()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-coupon.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-coupon.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-coupon.soft_delete', 1);

        // Give
        $now = Carbon::now()->setTimezone('Asia/Taipei');

        $faker = \Faker\Factory::create();

        // When
            $db_morph_1 = $this->repository->save([
                'serial'     => $faker->isbn10,
                'identifier' => $faker->slug,
                'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                'value'      => '',
                'begin_at'   => '2020-01-01 00:00:00',
                'end_at'     => '2025-01-01 01:00:00',
                'is_enabled' => 1
            ]);
        // Then
            $this->assertTrue( $this->service->checkAvailability($db_morph_1->id, $now) );

        // When
            $db_morph_2 = $this->repository->save([
                'serial'     => $faker->isbn10,
                'identifier' => $faker->slug,
                'operator'   => $faker->randomElement(config('wk-core.class.core.operator')::getCodes()),
                'value'      => '',
                'begin_at'   => '2020-01-01 00:00:00',
                'end_at'     => '2025-01-01 01:00:00',
                'is_enabled' => 0
            ]);
        // Then
            $this->assertTrue( ! $this->service->checkAvailability($db_morph_2->id, $now) );
    }
}
