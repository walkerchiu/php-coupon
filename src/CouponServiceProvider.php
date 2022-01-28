<?php

namespace WalkerChiu\Coupon;

use Illuminate\Support\ServiceProvider;

class CouponServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/coupon.php' => config_path('wk-coupon.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_coupon_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_coupon_table.php'
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-coupon');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-coupon'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-coupon.command.cleaner')
            ]);
        }

        config('wk-core.class.coupon.coupon')::observe(config('wk-core.class.coupon.couponObserver'));
        config('wk-core.class.coupon.couponLang')::observe(config('wk-core.class.coupon.couponLangObserver'));
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-coupon')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/coupon.php', 'wk-coupon'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/coupon.php', 'coupon'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
