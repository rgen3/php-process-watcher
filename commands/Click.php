<?php

namespace console\components\watcher\commands;

class Click extends AbstarctProcess
{
    public function beforeStop()
    {
        echo "Click will be stopped";
    }

    public function beforeStart()
    {
        echo "Click ready to start";
    }

    public static function getCmd() : string
    {
        return '/usr/bin/php /vagrant/yii importer/while';
    }

    public static function getDescriptor(): array
    {
        return [
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "a")
        ];
    }
}