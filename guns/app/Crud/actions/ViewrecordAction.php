<?php

class ViewrecordAction extends CrudAppController
{
    public function main($modelName, $id)
    {
        $data = $modelName::find($id);
        if ($data == false) {
            die('Unable to find Record with ID: ' . $id);
        }
        $data = Model::result_array($data);
        $this->set('data', $data[0]);
        $this->set('modelName', $modelName);
        $this->set('id', $id);
        $this->set('tableName', $modelName::$table_name);
    }
} 