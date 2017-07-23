<?php

class ExecProcess
{
    private $pid;
    private $log;
    private $command;
    private $root;

    private $envList = [];

    private $additional = [];

    private function runCom()
    {
        $command = "";
        if(!empty($this->envList)){
            $command .= 'export PATH=$PATH:'.implode(":", $this->envList).'; ';
        }
        if(!empty($this->root)){
            $$command .= 'cd '.$this->root.'; ';
        }
        if(!empty($this->command)){
            $command .= $this->command . ' > ' . $this->log . ' 2>&1 & echo $!;';
        }
        if(!empty($this->additional)){
            $command .= implode("; ", $this->additional);
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

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function setEnv($envList)
    {
        $this->envList = $envList;
    }

    public function setCommand($commandLine)
    {
        $this->command = $commandLine;
    }

    public function setAdditional($additionalCommands)
    {
        $this->additional = $additionalCommands;
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