<?php

class DbRoute extends Model
{
    static $connection = 'development';
    static $primary_key = 'id';
    static $table_name = 'db_routes';

    function before_create()
    {
        $this->created = date('Y-m-d H:i:s', time());
    }

    function before_update()
    {
        $this->updated = date('Y-m-d H:i:s', time());
    }
}