<?php

Configure::set('router.databaseRouterEnabled', false);

Configure::set('router.defaultController', 'Home');

Configure::set('router.defaultAction', 'Index');

Configure::set('router.routeModel', 'DbRoute');

loadClass('Router');
Router::initiate();

App::import('Routes', 'app/Crud');
