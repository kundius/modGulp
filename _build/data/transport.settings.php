<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();

$tmp = array(
    'bin_path' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'modgulp_main',
    ),
    'gulpfile' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'modgulp_main',
    ),
);

foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'modgulp_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;
