<?php

namespace WalkerChiu\Coupon\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class CouponRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.coupon.coupon'));
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(?string $host_type, ?string $host_id, string $code, array $data, $is_enabled = null, $target = null, $target_is_enabled = null, $auto_packing = false)
    {
        if (
            empty($host_type)
            || empty($host_id)
        ) {
            $instance = $this->instance;
        } else {
            $instance = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->with(['langs' => function ($query) use ($code) {
                                    $query->ofCurrent()
                                          ->ofCode($code);
                                }])
                                ->whereHas('langs', function ($query) use ($code) {
                                    return $query->ofCurrent()
                                                 ->ofCode($code);
                                })
                                ->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['identifier']), function ($query) use ($data) {
                                                return $query->where('identifier', $data['identifier']);
                                            })
                                            ->unless(empty($data['operator']), function ($query) use ($data) {
                                                return $query->where('operator', $data['operator']);
                                            })
                                            ->unless(empty($data['value']), function ($query) use ($data) {
                                                return $query->where('value', $data['value']);
                                            })
                                            ->unless(empty($data['order']), function ($query) use ($data) {
                                                return $query->where('order', $data['order']);
                                            })
                                            ->unless(empty($data['begin_at']), function ($query) use ($data) {
                                                return $query->where('begin_at', $data['begin_at']);
                                            })
                                            ->unless(empty($data['end_at']), function ($query) use ($data) {
                                                return $query->where('end_at', $data['end_at']);
                                            })
                                            ->unless(empty($data['only_dayType']), function ($query) use ($data) {
                                                return $query->where('only_dayType', $data['only_dayType']);
                                            })
                                            ->unless(empty($data['exclude_date']), function ($query) use ($data) {
                                                return $query->where('exclude_date', $data['exclude_date']);
                                            })
                                            ->unless(empty($data['exclude_time']), function ($query) use ($data) {
                                                return $query->where('exclude_time', $data['exclude_time']);
                                            })
                                            ->unless(empty($data['name']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'name')
                                                          ->where('value', 'LIKE', "%".$data['name']."%");
                                                });
                                            })
                                            ->unless(empty($data['description']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'description')
                                                          ->where('value', 'LIKE', "%".$data['description']."%");
                                                });
                                            })
                                            ->unless(empty($data['remarks']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'remarks')
                                                          ->where('value', 'LIKE', "%".$data['remarks']."%");
                                                });
                                            })
                                            ->unless(empty($data['categories']), function ($query) use ($data) {
                                                return $query->whereHas('categories', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['categories']);
                                                });
                                            })
                                            ->unless(empty($data['tags']), function ($query) use ($data) {
                                                return $query->whereHas('tags', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['tags']);
                                                });
                                            });
                                    })
                                ->orderBy('order', 'ASC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-coupon.output_format'), config('wk-coupon.pagination.pageName'), config('wk-coupon.pagination.perPage'));
            $factory->setFieldsLang(['name', 'description', 'remarks']);
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Coupon        $instance
     * @param String|Array  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $data = [
            'id' => $instance ? $instance->id : '',
            'basic' => []
        ];

        if (empty($instance))
            return $data;

        $this->setEntity($instance);

        if (is_string($code)) {
            $data['basic'] = [
                  'host_type'      => $instance->host_type,
                  'host_id'        => $instance->host_id,
                  'serial'         => $instance->serial,
                  'identifier'     => $instance->identifier,
                  'operator'       => $instance->operator,
                  'value'          => $instance->value,
                  'options'        => $instance->options,
                  'images'         => $instance->images,
                  'begin_at'       => $instance->begin_at,
                  'end_at'         => $instance->end_at,
                  'only_dayType'   => $instance->only_dayType,
                  'exclude_date'   => $instance->exclude_date,
                  'exclude_time'   => $instance->exclude_time,
                  'use_per_order'  => $instance->use_per_order,
                  'use_per_guest'  => $instance->use_per_guest,
                  'use_per_member' => $instance->use_per_member,
                  'name'           => $instance->findLang($code, 'name'),
                  'description'    => $instance->findLang($code, 'description'),
                  'remarks'        => $instance->findLang($code, 'remarks'),
                  'order'          => $instance->order,
                  'is_enabled'     => $instance->is_enabled,
                  'updated_at'     => $instance->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                      'host_type'      => $instance->host_type,
                      'host_id'        => $instance->host_id,
                      'serial'         => $instance->serial,
                      'identifier'     => $instance->identifier,
                      'operator'       => $instance->operator,
                      'value'          => $instance->value,
                      'options'        => $instance->options,
                      'images'         => $instance->images,
                      'begin_at'       => $instance->begin_at,
                      'end_at'         => $instance->end_at,
                      'only_dayType'   => $instance->only_dayType,
                      'exclude_date'   => $instance->exclude_date,
                      'exclude_time'   => $instance->exclude_time,
                      'use_per_order'  => $instance->use_per_order,
                      'use_per_guest'  => $instance->use_per_guest,
                      'use_per_member' => $instance->use_per_member,
                      'name'           => $instance->findLang($language, 'name'),
                      'description'    => $instance->findLang($language, 'description'),
                      'remarks'        => $instance->findLang($language, 'remarks'),
                      'order'          => $instance->order,
                      'is_enabled'     => $instance->is_enabled,
                      'updated_at'     => $instance->updated_at
                ];
            }
        }

        return $data;
    }
}
