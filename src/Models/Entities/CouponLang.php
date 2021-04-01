<?php

namespace WalkerChiu\Coupon\Models\Entities;

use WalkerChiu\Core\Models\Entities\Lang;

class CouponLang extends Lang
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->table = config('wk-core.table.coupon.coupons_lang');

        parent::__construct($attributes);
    }
}
