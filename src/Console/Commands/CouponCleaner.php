<?php

namespace WalkerChiu\Coupon\Console\Commands;

use WalkerChiu\Core\Console\Commands\Cleaner;

class CouponCleaner extends Cleaner
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CouponCleaner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate tables';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::clean('coupon');
    }
}
