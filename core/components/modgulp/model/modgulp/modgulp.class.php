<?php

require_once 'execprocess.class.php';

class modGulp
{
    /** @var modX $modx */
    public $modx;

    private $tempLogFile;
    private $tempPidFile;

    /** @var  ExecProcess */
    private $execProcess;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $this->modx->lexicon->load('modgulp:default');

        $corePath = $this->modx->getOption('modgulp_core_path', $config,
            $this->modx->getOption('core_path') . 'components/modgulp/'
        );
        $assetsUrl = $this->modx->getOption('modgulp_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/modgulp/'
        );

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'img/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/'
        ), $config);

        $this->tempPidFile = $corePath . '/tmp/pid';
        $this->tempLogFile = $corePath . '/tmp/log';

        $this->execProcess = new ExecProcess();
        $this->execProcess->setLogFile($this->tempLogFile);
        $this->execProcess->setRoot($this->modx->getOption('modgulp_root'));
        $this->execProcess->setEnv([$this->modx->getOption('modgulp_bin_path')]);
        $this->execProcess->setCommand('gulp');

        $this->config['isActive'] = $this->isActive();
    }

    public function start()
    {
        if(!$this->isActive()){
            $this->execProcess->start();
            $this->setPid();
            return $this->checkStatus();
        }
        return true;
    }

    public function stop()
    {
        if($this->isActive()){
            $this->execProcess->stop();
            $this->clearPid();
            $this->clearLog();
        }
        return true;
    }

    public function getLog()
    {
        if(is_file($this->tempLogFile)){
            return file_get_contents($this->tempLogFile);
        }
        return false;
    }

    public function isActive()
    {
        return $this->checkStatus();
    }

    private function getPid()
    {
        if(is_file($this->tempPidFile)){
            $pid = file_get_contents($this->tempPidFile);
            $this->execProcess->setPid($pid);
            return $pid;
        }
        return null;
    }

    private function setPid()
    {
        file_put_contents($this->tempPidFile, $this->execProcess->getPid());
    }

    private function clearPid()
    {
        if(is_file($this->tempPidFile)){
            unlink($this->tempPidFile);
        }
        $this->execProcess->setPid(null);
    }
    private function clearLog()
    {
        if(is_file($this->tempLogFile)){
            unlink($this->tempLogFile);
        }
    }

    private function checkStatus()
    {
        $pid = $this->getPid();
        if(!empty($pid)){
            if($this->execProcess->status()){
                return true;
            }
            $this->clearPid();
            // $this->clearLog();
            return false;
        }
        return false;
    }

}
