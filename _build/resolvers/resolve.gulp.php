<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    $node = false;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $node = true;
            break;

        case xPDOTransport::ACTION_UPGRADE:
            if (!empty($options['update_node'])) {
                $node = true;
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }

    if($node) {
        $vendorPath = $modx->getOption('modgulp_core_path', null, $modx->getOption('core_path') . 'components/modgulp/') . 'vendor/';
        
        $versions = json_decode(file_get_contents('https://nodejs.org/dist/index.json'));
        $node_latest = 'node-' . $versions[0]->version . '-linux-x64';
        $node_bin = $vendorPath . $node_latest . '/bin';
        $commands = array(
            'cd ' . $vendorPath,
            'wget https://nodejs.org/dist/latest/' . $node_latest . '.tar.gz',
            'tar xzf ' . $node_latest . '.tar.gz',
            'rm ' . $node_latest . '.tar.gz'
        );

        exec(implode('; ', $commands));
        
        if(file_exists($node_bin)) {
            $modx->log(modX::LOG_LEVEL_INFO, 'Node installed!');

            $setting = $modx->getObject('modSystemSetting', 'modgulp_bin_path');
            $setting->set('value', $node_bin);
            $setting->save();
        } else {
            $modx->log(modX::LOG_LEVEL_ERROR, 'Node is not installed!');
        }
    }
}
return true;
