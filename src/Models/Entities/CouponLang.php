<?php

namespace WalkerChiu\Coupon\Models\Entities;

use WalkerChiu\Core\Models\Entities\Lang;

class CouponLang extends Lang
{
    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.coupon.coupons_lang');

        parent::__construct($attributes);
    }
}
