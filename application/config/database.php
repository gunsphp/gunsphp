<?php

Configure::set('database.defaultDriver', 'mysql');

class DB
{

    public static function getDbConfig()
    {
        return array(
            'mysql' => array(
                'development' => 'mysql://root:@127.0.0.1/gunsphp',
                'production' => 'mysql://root:@127.0.0.1/gunsphp',
                'test' => 'mysql://username:password@localhost/test_database_name',
                'production' => 'mysql://username:password@localhost/production_database_name'
            ),
            'pgsql' => array(
                'development' => 'pgsql://username:password@localhost/development',
                'test' => 'pgsql://username:password@localhost/test',
                'production' => 'pgsql://username:password@localhost/production',
            ),
            'sqlite' => array(
                'development' => 'sqlite://development.db',
                'test' => 'sqlite://test.db',
                'production' => 'sqlite://production.db',
            ),
            'oci' => array(
                'development' => 'oci://username:passsword@localhost/xe',
                'test' => 'oci://username:passsword@localhost/xe',
                'production' => 'oci://username:passsword@localhost/xe',
            ),
        );
    }

}

/*
 *
 * DB Route DDL
 * For Dynamic Database Routes
 *
 * CREATE TABLE `db_routes` (
      `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '{"primary":true,"display":true}',
      `router_name` varchar(100) NOT NULL COMMENT '{"type":"text","required":true,"display":true}',
      `router_url` varchar(100) NOT NULL COMMENT '{"type":"text","required":true,"display":true}',
      `controller_name` varchar(100) NOT NULL COMMENT '{"type":"text","required":true,"display":true}',
      `action_name` varchar(100) DEFAULT 'Index' COMMENT '{"type":"text","required":true,"default":"Index","display":true}',
      `created` datetime DEFAULT NULL COMMENT '{"readonly":true,"type":"datetime","autoload":true}',
      `updated` datetime DEFAULT NULL COMMENT '{"readonly":true,"type":"datetime","autoload":true}',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
 *
 */