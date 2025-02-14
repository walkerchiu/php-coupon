<?php

namespace WalkerChiu\Coupon;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use WalkerChiu\Coupon\Models\Entities\Coupon;
use WalkerChiu\Coupon\Models\Entities\CouponLang;

class CouponTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
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
     * A basic functional test on Coupon.
     *
     * For WalkerChiu\Coupon\Models\Entities\Coupon
     *
     * @return void
     */
    public function testCoupon()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-coupon.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-coupon.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-coupon.soft_delete', 1);

        // Give
        $db_morph_1 = factory(Coupon::class)->create();
        $db_morph_2 = factory(Coupon::class)->create();
        $db_morph_3 = factory(Coupon::class)->create(['is_enabled' => 1]);

        // Get records after creation
            // When
            $records = Coupon::all();
            // Then
            $this->assertCount(3, $records);

        // Delete someone
            // When
            $db_morph_2->delete();
            $records = Coupon::all();
            // Then
            $this->assertCount(2, $records);

        // Resotre someone
            // When
            Coupon::withTrashed()
                  ->find($db_morph_2->id)
                  ->restore();
            $record_2 = Coupon::find($db_morph_2->id);
            $records = Coupon::all();
            // Then
            $this->assertNotNull($record_2);
            $this->assertCount(3, $records);

        // Return Lang class
            // When
            $class = $record_2->lang();
            // Then
            $this->assertEquals($class, CouponLang::class);

        // Scope query on enabled records
            // When
            $records = Coupon::ofEnabled()
                             ->get();
            // Then
            $this->assertCount(1, $records);

        // Scope query on disabled records
            // When
            $records = Coupon::ofDisabled()
                             ->get();
            // Then
            $this->assertCount(2, $records);
    }
}
