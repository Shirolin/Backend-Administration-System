<?php

return [

    'accepted'             => ':attribute 必须接受。',
    'active_url'           => ':attribute 不是一个有效的网址。',
    'after'                => ':attribute 必须是 :date 之后的日期。',
    'after_or_equal'       => ':attribute 必须是 :date 之后或相同的日期。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母、数字和短划线。',
    'alpha_num'            => ':attribute 只能包含字母和数字。',
    'array'                => ':attribute 必须是一个数组。',
    'before'               => ':attribute 必须是 :date 之前的日期。',
    'before_or_equal'      => ':attribute 必须是 :date 之前或相同的日期。',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 到 :max 之间。',
        'file'    => ':attribute 必须在 :min 到 :max 千字节之间。',
        'string'  => ':attribute 必须在 :min 到 :max 个字符之间。',
        'array'   => ':attribute 必须有 :min 到 :max 项。',
    ],
    'boolean'              => ':attribute 字段必须为 true 或 false。',
    'confirmed'            => ':attribute 确认不匹配。',
    'date'                 => ':attribute 不是一个有效的日期。',
    'date_format'          => ':attribute 不符合格式 :format。',
    'different'            => ':attribute 和 :other 必须不同。',
    'digits'               => ':attribute 必须是 :digits 位数字。',
    'digits_between'       => ':attribute 必须在 :min 到 :max 位数字之间。',
    'dimensions'           => ':attribute 图片尺寸无效。',
    'distinct'             => ':attribute 字段有重复值。',
    'email'                => ':attribute 必须是一个有效的电子邮件地址。',
    'exists'               => '选择的 :attribute 无效。',
    'file'                 => ':attribute 必须是一个文件。',
    'filled'               => ':attribute 字段必须有值。',
    'image'                => ':attribute 必须是图片。',
    'in'                   => '选择的 :attribute 无效。',
    'in_array'             => ':attribute 字段不存在于 :other 中。',
    'integer'              => ':attribute 必须是整数。',
    'ip'                   => ':attribute 必须是一个有效的 IP 地址。',
    'ipv4'                 => ':attribute 必须是一个有效的 IPv4 地址。',
    'ipv6'                 => ':attribute 必须是一个有效的 IPv6 地址。',
    'json'                 => ':attribute 必须是一个有效的 JSON 字符串。',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max。',
        'file'    => ':attribute 不能大于 :max 千字节。',
        'string'  => ':attribute 不能大于 :max 个字符。',
        'array'   => ':attribute 不能有超过 :max 项。',
    ],
    'mimes'                => ':attribute 必须是一个 :values 类型的文件。',
    'mimetypes'            => ':attribute 必须是一个 :values 类型的文件。',
    'min'                  => [
        'numeric' => ':attribute 必须至少是 :min。',
        'file'    => ':attribute 必须至少是 :min 千字节。',
        'string'  => ':attribute 必须至少是 :min 个字符。',
        'array'   => ':attribute 必须至少有 :min 项。',
    ],
    'not_in'               => '选择的 :attribute 无效。',
    'numeric'              => ':attribute 必须是一个数字。',
    'present'              => ':attribute 字段必须存在。',
    'regex'                => ':attribute 格式无效。',
    'required'             => ':attribute 字段是必需的。',
    'required_if'          => '当 :other 是 :value 时，:attribute 字段是必需的。',
    'required_unless'      => '除非 :other 在 :values 中，否则 :attribute 字段是必需的。',
    'required_with'        => '当 :values 存在时，:attribute 字段是必需的。',
    'required_with_all'    => '当 :values 存在时，:attribute 字段是必需的。',
    'required_without'     => '当 :values 不存在时，:attribute 字段是必需的。',
    'required_without_all' => '当 :values 都不存在时，:attribute 字段是必需的。',
    'same'                 => ':attribute 和 :other 必须匹配。',
    'size'                 => [
        'numeric' => ':attribute 必须是 :size。',
        'file'    => ':attribute 必须是 :size 千字节。',
        'string'  => ':attribute 必须是 :size 个字符。',
        'array'   => ':attribute 必须包含 :size 项。',
    ],
    'string'               => ':attribute 必须是一个字符串。',
    'timezone'             => ':attribute 必须是一个有效的区域。',
    'unique'               => ':attribute 已经被占用。',
    'uploaded'             => ':attribute 上传失败。',
    'url'                  => ':attribute 格式无效。',

    'custom' => [
        'attribute-name' => [
            'rule-name' => '自定义消息',
        ],
    ],

    'attributes' => [],

];