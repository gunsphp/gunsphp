<?php

class IndexAction extends HomeAppController
{

    public function main()
    {
        $this->set('actionFilePath', APP_DIR . DS . 'Home' . DS . 'actions' . DS . 'IndexAction.php');
        $this->set('actionViewPath', APP_DIR . DS . 'Home' . DS . 'views' . DS . 'Index.html.twig');
    }

    public function onload()
    {
        $this['#foo']->setHtml('GunsPHP has been Successfully Installed and Initiated!')->addClass('highlight');
    }
} 