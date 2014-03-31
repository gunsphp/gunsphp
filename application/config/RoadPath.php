<?php

return array(
    'http' => array(
        'beforeMainController' => array(
            'beforeFilter' => array(
                'acceptsParams' => false,
                'allowedControllers' => '*',
                'allowedActions' => '*'
            )
        ),
        'afterMainController' => array(
            'afterFilter' => array(
                'acceptsParams' => false,
                'allowedControllers' => '*',
                'allowedActions' => '*'
            )
        ),
        'beforeViewRender' => array(
            'beforeRender' => array(
                'acceptsParams' => false,
                'allowedControllers' => '*',
                'allowedActions' => '*'
            )
        ),
        'afterViewRender' => array(
            'acceptsParams' => false,
            'allowedControllers' => '*',
            'allowedActions' => '*'
        )
    ),
    'ajax' => array()
);