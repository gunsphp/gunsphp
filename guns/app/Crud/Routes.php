<?php

Router::set('administer/:modelName/create', array(
    'controller' => 'Crud',
    'action' => 'Create'
), 'crud_create');

Router::set('administer/:modelName/list', array(
    'controller' => 'Crud',
    'action' => 'Read'
), 'crud_read');

Router::set('administer/:modelName/update/:id', array(
    'controller' => 'Crud',
    'action' => 'Update'
), 'crud_update');

Router::set('administer/:modelName/delete/:id', array(
    'controller' => 'Crud',
    'action' => 'Delete'
), 'crud_delete');

Router::set('administer/:modelName/viewrecord/:id', array(
    'controller' => 'Crud',
    'action' => 'Viewrecord'
), 'crud_view');

Router::set('administer/:modelName/configure', array(
    'controller' => 'Crud',
    'action' => 'Configure'
), 'crud_configure');

Router::set('administer/crud/listall', array(
    'controller' => 'Crud',
    'action' => 'Listmodels'
), 'crud_listall');