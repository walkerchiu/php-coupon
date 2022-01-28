<?php

namespace WalkerChiu\Coupon\Models\Entities;

use WalkerChiu\Core\Models\Entities\LangTrait;
use WalkerChiu\Core\Models\Entities\UuidEntity;

class Coupon extends UuidEntity
{
    use LangTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.coupon.coupons');

        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'serial', 'identifier',
            'operator', 'value',
            'options', 'images',
            'begin_at', 'end_at',
            'only_dayType', 'exclude_date', 'exclude_time',
            'use_per_order', 'use_per_guest', 'use_per_member',
            'order'
        ]);

        $this->dates = array_merge($this->dates, [
            'begin_at', 'end_at'
        ]);

        $this->casts = array_merge($this->casts, [
            'only_dayType' => 'json',
            'exclude_date' => 'json',
            'exclude_time' => 'json'
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get it's lang entity.
     *
     * @return Lang
     */
    public function lang()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-coupon.onoff.core-lang_core')
        ) {
            return config('wk-core.class.core.langCore');
        } else {
            return config('wk-core.class.coupon.couponLang');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-coupon.onoff.core-lang_core')
        ) {
            return $this->langsCore();
        } else {
            return $this->hasMany(config('wk-core.class.coupon.couponLang'), 'morph_id', 'id');
        }
    }

    /**
     * Get the owning host model.
     */
    public function host()
    {
        return $this->morphTo();
    }
}
