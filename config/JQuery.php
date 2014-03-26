<?php

Configure::set('jQuery.logFilePath', 'logs');

Configure::set('jQuery.script.useInline', true);

Configure::set('jQuery.ajax.default.options', array(
    'type' => 'POST',
    'cache' => true,
    'async' => true
));

Configure::set('jQuery.ajax.logging', array(
    'consolelog' => true,
    'logfile' => false
));

Configure::set('jQuery.events.render', array(
    'onClick' => 'click',
    'onChange' => 'change',
    'onDoubleClick' => 'dblclick',
    'onSubmit' => 'submit'
));