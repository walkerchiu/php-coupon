<?php

namespace WalkerChiu\Coupon\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class CouponFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (string) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'host_type'  => trans('php-coupon::coupon.host_type'),
            'host_id'    => trans('php-coupon::coupon.host_id'),
            'serial'     => trans('php-coupon::coupon.serial'),
            'identifier' => trans('php-coupon::coupon.identifier'),
            'operator'   => trans('php-coupon::coupon.operator'),
            'value'      => trans('php-coupon::coupon.value'),
            'options'    => trans('php-coupon::coupon.options'),
            'images'     => trans('php-coupon::coupon.images'),
            'order'      => trans('php-coupon::coupon.order'),
            'is_enabled' => trans('php-coupon::coupon.is_enabled'),

            'begin_at'       => trans('php-coupon::coupon.begin_at'),
            'end_at'         => trans('php-coupon::coupon.end_at'),
            'only_dayType'   => trans('php-coupon::coupon.only_dayType'),
            'exclude_date'   => trans('php-coupon::coupon.exclude_date'),
            'exclude_time'   => trans('php-coupon::coupon.exclude_time'),
            'use_per_order'  => trans('php-coupon::coupon.use_per_order'),
            'use_per_guest'  => trans('php-coupon::coupon.use_per_guest'),
            'use_per_member' => trans('php-coupon::coupon.use_per_member'),

            'name'        => trans('php-coupon::coupon.name'),
            'description' => trans('php-coupon::coupon.description'),
            'remarks'     => trans('php-coupon::coupon.remarks')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'host_type'  => 'required_with:host_id|string',
            'host_id'    => 'required_with:host_type|string',
            'serial'     => '',
            'identifier' => 'required|string|max:255',
            'operator'   => ['required', Rule::in(config('wk-core.class.core.operator')::getCodes())],
            'value'      => '',
            'options'    => 'nullable|json',
            'images'     => 'nullable|json',
            'order'      => 'nullable|numeric|min:0',
            'is_enabled' => 'boolean',

            'begin_at'       => 'required|date|date_format:Y-m-d H:i:s|before:end_at',
            'end_at'         => 'required|date|date_format:Y-m-d H:i:s|after:begin_at',
            'only_dayType'   => 'nullable|array|min:1|max:7',
            'only_dayType.*' => 'required|integer|distinct|between:0,7',
            'exclude_date'   => 'nullable|array',
            'exclude_date.*' => 'date|distinct|date_format:Y-m-d',
            'exclude_time'   => 'nullable|array',
            'exclude_time.*' => 'date|distinct|date_format:H:i:s',
            'use_per_order'  => 'nullable|numeric|min:0',
            'use_per_guest'  => 'nullable|numeric|min:0',
            'use_per_member' => 'nullable|numeric|min:0',

            'name'        => 'required|string|max:255',
            'description' => '',
            'remarks'     => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','string','exists:'.config('wk-core.table.coupon.coupons').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'             => trans('php-core::validation.required'),
            'id.string'               => trans('php-core::validation.string'),
            'id.exists'               => trans('php-core::validation.exists'),
            'host_type.required_with' => trans('php-core::validation.required_with'),
            'host_type.string'        => trans('php-core::validation.string'),
            'host_id.required_with'   => trans('php-core::validation.required_with'),
            'host_id.string'          => trans('php-core::validation.string'),
            'identifier.required'     => trans('php-core::validation.required'),
            'identifier.string'       => trans('php-core::validation.required'),
            'identifier.max'          => trans('php-core::validation.max'),
            'operator.required'       => trans('php-core::validation.required'),
            'operator.in'             => trans('php-core::validation.in'),
            'options.json'            => trans('php-core::validation.json'),
            'images.json'             => trans('php-core::validation.json'),
            'order.numeric'           => trans('php-core::validation.numeric'),
            'order.min'               => trans('php-core::validation.min'),
            'is_enabled.boolean'      => trans('php-core::validation.boolean'),

            'begin_at.required'          => trans('php-core::validation.required'),
            'begin_at.date'              => trans('php-core::validation.date'),
            'begin_at.date_format'       => trans('php-core::validation.date_format'),
            'begin_at.before'            => trans('php-core::validation.before'),
            'end_at.required'            => trans('php-core::validation.required'),
            'end_at.date'                => trans('php-core::validation.date'),
            'end_at.date_format'         => trans('php-core::validation.date_format'),
            'end_at.after'               => trans('php-core::validation.after'),
            'only_dayType.array'         => trans('php-core::validation.array'),
            'only_dayType.min'           => trans('php-core::validation.min'),
            'only_dayType.max'           => trans('php-core::validation.max'),
            'only_dayType.*.required'    => trans('php-core::validation.required'),
            'only_dayType.*.integer'     => trans('php-core::validation.integer'),
            'only_dayType.*.distinct'    => trans('php-core::validation.distinct'),
            'only_dayType.*.between'     => trans('php-core::validation.between'),
            'exclude_date.array'         => trans('php-core::validation.array'),
            'exclude_date.*.date'        => trans('php-core::validation.date'),
            'exclude_date.*.distinct'    => trans('php-core::validation.distinct'),
            'exclude_date.*.date_format' => trans('php-core::validation.date_format'),
            'exclude_time.array'         => trans('php-core::validation.array'),
            'exclude_time.*.date'        => trans('php-core::validation.date'),
            'exclude_time.*.distinct'    => trans('php-core::validation.distinct'),
            'exclude_time.*.date_format' => trans('php-core::validation.date_format'),
            'use_per_order.numeric'      => trans('php-core::validation.numeric'),
            'use_per_order.min'          => trans('php-core::validation.min'),
            'use_per_guest.numeric'      => trans('php-core::validation.numeric'),
            'use_per_guest.min'          => trans('php-core::validation.min'),
            'use_per_member.numeric'     => trans('php-core::validation.numeric'),
            'use_per_member.min'         => trans('php-core::validation.min'),

            'name.required' => trans('php-core::validation.required'),
            'name.string'   => trans('php-core::validation.string'),
            'name.max'      => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (
                isset($data['host_type'])
                && isset($data['host_id'])
            ) {
                if (
                    config('wk-coupon.onoff.site')
                    && !empty(config('wk-core.class.site.site'))
                    && $data['host_type'] == config('wk-core.class.site.site')
                ) {
                    $result = DB::table(config('wk-core.table.site.sites'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                } elseif (
                    config('wk-coupon.onoff.group')
                    && !empty(config('wk-core.class.group.group'))
                    && $data['host_type'] == config('wk-core.class.group.group')
                ) {
                    $result = DB::table(config('wk-core.table.group.groups'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                }
            }
        });
    }
}
