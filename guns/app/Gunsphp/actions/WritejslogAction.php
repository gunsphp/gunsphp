<?php

Class WritejslogAction extends GunsPHPAppController
{
    public $libraries = array('Request');

    public function main()
    {
        die('Direct Access to System Controller Not Allowed!');
    }

    public function performwrite()
    {
        $logFolder = ROOT . DS . Configure::get('cache.dir') . DS . Configure::get('jQuery.logFilePath');
        if (!is_dir($logFolder)) {
            mkdir($logFolder, 0777, true);
            chmod($logFolder, 0777);
        }
        $logFileName = "js_" . date('Ymd') . ".log";
        $script = $this->Request->post('logdata');
        $url = $this->Request->post('calledfromUrl');
        $log = "\n\nJS LOG: $url\n---------------------------------------------------------------------------\n";
        $log .= $script;
        file_put_contents($logFolder . DS . $logFileName, $log, FILE_APPEND);
    }
}