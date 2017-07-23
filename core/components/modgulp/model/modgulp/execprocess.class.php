<?php

class ExecProcess
{
    private $pid;
    private $log;
    private $command;
    private $gulpfile;

    private $envList = [];

    private function runCom()
    {
        $command = "";
        if(!empty($this->envList)){
            $command .= 'export PATH=$PATH:' . implode(":", $this->envList) . '; ';
        }
        if(!empty($this->command)){
            $gulpfile = !empty($this->gulpfile) ? ' --gulpfile ' . $this->gulpfile : '';
            $command .= $this->command . $gulpfile . ' > ' . $this->log . ' 2>&1 & echo $!;';
        }
        exec($command, $op);
        $this->pid = $op[0];
    }

    public function setLogFile($file)
    {
        $this->log = $file?$file:'/dev/null';
    }

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function setGulpfile($gulpfile)
    {
        $this->gulpfile = $gulpfile;
    }

    public function setEnv($envList)
    {
        $this->envList = $envList;
    }

    public function setCommand($commandLine)
    {
        $this->command = $commandLine;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function status()
    {
        if(empty($this->pid)) return false;
        $command = 'ps -p ' . $this->pid;
        exec($command, $op);
        return isset($op[1]);
    }

    public function start()
    {
        $this->runCom();
    }

    public function stop()
    {
        if(empty($this->pid)) return true;
        $command = 'kill ' . $this->pid;
        exec($command);
        return !$this->status();
    }
}
