<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkCouponTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.coupon.coupons'), function (Blueprint $table) {
            $table->uuid('id');
            $table->nullableUuidMorphs('host');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->string('operator', 10);
            $table->string('value');
            $table->json('options')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('begin_at');
            $table->timestamp('end_at');
            $table->json('only_dayType')->nullable();
            $table->json('exclude_date')->nullable();
            $table->json('exclude_time')->nullable();
            $table->unsignedBigInteger('use_per_order')->nullable();
            $table->unsignedBigInteger('use_per_guest')->nullable();
            $table->unsignedBigInteger('use_per_member')->nullable();
            $table->unsignedBigInteger('order')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
        });
        if (!config('wk-coupon.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.coupon.coupons_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->uuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.coupon.coupons_lang'));
        Schema::dropIfExists(config('wk-core.table.coupon.coupons'));
    }
}
