<?php

namespace WalkerChiu\Coupon\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;

class CouponRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;

    protected $entity;
    protected $morphType;

    public function __construct()
    {
        $this->entity = App::make(config('wk-core.class.coupon.coupon'));
    }

    /**
     * @param String  $host_type
     * @param String  $host_id
     * @param String  $code
     * @param Array   $data
     * @param Int     $page
     * @param Int     $nums per page
     * @param Boolean $is_enabled
     * @param String  $target
     * @param Boolean $target_is_enabled
     * @param Boolean $toArray
     * @return Array|Collection
     */
    public function list($host_type, $host_id, String $code, Array $data, $page = null, $nums = null, $is_enabled = null, $target = null, $target_is_enabled = null, $toArray = true)
    {
        $this->assertForPagination($page, $nums);

        if (empty($host_type) || empty($host_id)) {
            $entity = $this->entity;
        } else {
            $entity = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $entity = $entity->ofEnabled();
        elseif ($is_enabled === false) $entity = $entity->ofDisabled();

        $data = array_map('trim', $data);
        $records = $entity->with(['langs' => function ($query) use ($code) {
                                $query->ofCurrent()
                                      ->ofCode($code);
                            }])
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
                                          return $query->whereHas('langs', function($query) use ($data) {
                                              $query->ofCurrent()
                                                    ->where('key', 'name')
                                                    ->where('value', 'LIKE', "%".$data['name']."%");
                                          });
                                      })
                                      ->unless(empty($data['description']), function ($query) use ($data) {
                                          return $query->whereHas('langs', function($query) use ($data) {
                                              $query->ofCurrent()
                                                    ->where('key', 'description')
                                                    ->where('value', 'LIKE', "%".$data['description']."%");
                                          });
                                      })
                                      ->unless(empty($data['remarks']), function ($query) use ($data) {
                                          return $query->whereHas('langs', function($query) use ($data) {
                                              $query->ofCurrent()
                                                    ->where('key', 'remarks')
                                                    ->where('value', 'LIKE', "%".$data['remarks']."%");
                                          });
                                      })
                                      ->unless(empty($data['categories']), function ($query) use ($data) {
                                          return $query->whereHas('categories', function($query) use ($data) {
                                              $query->ofEnabled()
                                                    ->whereIn('id', $data['categories']);
                                          });
                                      })
                                      ->unless(empty($data['tags']), function ($query) use ($data) {
                                          return $query->whereHas('tags', function($query) use ($data) {
                                              $query->ofEnabled()
                                                    ->whereIn('id', $data['tags']);
                                          });
                                      });
                            })
                          ->orderBy('order', 'ASC')
                          ->get()
                          ->when(is_integer($page) && is_integer($nums), function ($query) use ($page, $nums) {
                              return $query->forPage($page, $nums);
                          });
        if ($toArray) {
            $list = [];
            foreach ($records as $record) {
                $data = $record->toArray();
                array_push($list,
                    array_merge($data, [
                        'name'        => $record->findLangByKey('name'),
                        'description' => $record->findLangByKey('description'),
                        'remarks'     => $record->findLangByKey('remarks')
                    ])
                );
            }

            return $list;
        } else {
            return $records;
        }
    }

    /**
     * @param Coupon $entity
     * @param String|Array $code
     * @return Array
     */
    public function show($entity, $code)
    {
        $data = [
            'id' => $entity ? $entity->id : '',
            'basic' => []
        ];

        if (empty($entity))
            return $data;

        $this->setEntity($entity);

        if (is_string($code)) {
            $data['basic'] = [
                  'host_type'      => $entity->host_type,
                  'host_id'        => $entity->host_id,
                  'serial'         => $entity->serial,
                  'identifier'     => $entity->identifier,
                  'operator'       => $entity->operator,
                  'value'          => $entity->value,
                  'options'        => $entity->options,
                  'images'         => $entity->images,
                  'begin_at'       => $entity->begin_at,
                  'end_at'         => $entity->end_at,
                  'only_dayType'   => $entity->only_dayType,
                  'exclude_date'   => $entity->exclude_date,
                  'exclude_time'   => $entity->exclude_time,
                  'use_per_order'  => $entity->use_per_order,
                  'use_per_guest'  => $entity->use_per_guest,
                  'use_per_member' => $entity->use_per_member,
                  'name'           => $entity->findLang($code, 'name'),
                  'description'    => $entity->findLang($code, 'description'),
                  'remarks'        => $entity->findLang($code, 'remarks'),
                  'order'          => $entity->order,
                  'is_enabled'     => $entity->is_enabled,
                  'updated_at'     => $entity->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                      'host_type'      => $entity->host_type,
                      'host_id'        => $entity->host_id,
                      'serial'         => $entity->serial,
                      'identifier'     => $entity->identifier,
                      'operator'       => $entity->operator,
                      'value'          => $entity->value,
                      'options'        => $entity->options,
                      'images'         => $entity->images,
                      'begin_at'       => $entity->begin_at,
                      'end_at'         => $entity->end_at,
                      'only_dayType'   => $entity->only_dayType,
                      'exclude_date'   => $entity->exclude_date,
                      'exclude_time'   => $entity->exclude_time,
                      'use_per_order'  => $entity->use_per_order,
                      'use_per_guest'  => $entity->use_per_guest,
                      'use_per_member' => $entity->use_per_member,
                      'name'           => $entity->findLang($language, 'name'),
                      'description'    => $entity->findLang($language, 'description'),
                      'remarks'        => $entity->findLang($language, 'remarks'),
                      'order'          => $entity->order,
                      'is_enabled'     => $entity->is_enabled,
                      'updated_at'     => $entity->updated_at
                ];
            }
        }

        return $data;
    }
}
