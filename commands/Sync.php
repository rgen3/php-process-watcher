<?php

namespace console\components\watcher\commands;

class Sync extends AbstarctProcess
{

    public static $descriptor;

    public function beforeStart()
    {
        echo "Sync is ready to start";
    }

    public function beforeStop()
    {
        echo "Sync will be stopped";
    }

    public static function getCmd(): string
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