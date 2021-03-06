<?php

class modGulpStopProcessor extends modProcessor {
    public $languageTopics = array('modgulp');

    public function checkPermissions() {
        return $this->modx->hasPermission('file_create');
    }
    
    public function process() {
		$modGulp = $this->modx->getService('modgulp','modGulp',$this->modx->getOption('modgulp_core_path',null,$this->modx->getOption('core_path').'components/modgulp/').'model/modgulp/');
        $modGulp->stop();

    	return $this->success();
    }

}

return 'modGulpStopProcessor';