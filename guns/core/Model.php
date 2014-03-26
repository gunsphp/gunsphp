<?php

class Model extends ActiveRecord\Model
{
    function before_create()
    {
        $this->created = date('Y-m-d H:i:s', time());
    }

    function before_update()
    {
        $this->updated = date('Y-m-d H:i:s', time());
    }

    public static function result_array($data)
    {
        if ($data == null) {
            return null;
        }
        if (is_array($data)) {
            foreach ($data as $d) {
                $returnData[] = json_decode($d->to_json(), true);
            }
        } else {
            $returnData[] = json_decode($data->to_json(), true);
        }
        return $returnData;
    }

    public static function getColumnInfo($tableName, $columnName = null)
    {
        self::$table_name = $tableName;
        $sql = "SHOW FULL COLUMNS FROM $tableName";
        if ($columnName !== null) {
            $sql .= " WHERE field = '$columnName'";
        }
        $columns = Model::find_by_sql($sql);
        return self::result_array($columns);
    }

    public static function availableModels()
    {
        $modelDir = COMMON_DIR . DS . 'Models';
        $availableFiles = scandir($modelDir);
        unset($availableFiles[0]);
        unset($availableFiles[1]);
        foreach ($availableFiles as $file) {
            if (Text::endsWith($file, ".php"))
                $files[] = str_replace(".php", "", $file);
        }
        return $files;
    }
} 