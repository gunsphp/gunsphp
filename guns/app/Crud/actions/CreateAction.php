<?php

class CreateAction extends CrudAppController
{

    public function main($modelName)
    {
        $columns = Model::getColumnInfo($modelName::$table_name);
        $fields = array();
        foreach ($columns as $key => $column) {
            $columnComments = strlen($column['comment']) > 0 ? json_decode($column['comment'], true) : false;
            $f = array();
            if ($columnComments !== false) {
                $f['name'] = $column['field'];
                $f['type'] = isset($columnComments['type']) ? $columnComments['type'] : 'text';
                unset($columnComments['type']);
                $f = array_merge($f, $columnComments);
                $fields[] = $f;
            }
        }
        $this->set('fields', $fields);
        $this->set('modelName', $modelName);
        $this->set('tableName', $modelName::$table_name);
        $this->set('post', array(
            'onload' => array(
                'modelName' => $modelName,
                'fields' => $fields
            )
        ));
    }

    public function updateform_onSubmit()
    {
        $this['#loadingdiv']
            ->setHtml('Adding a new Record. Please Wait...')
            ->fadeIn();

        ajax('crud/create?call=addData', array(
            'data' => callback('serialize_form', array('#updateform'))
        ));

        return_false();
    }

    public function addData()
    {
        $postData = $this->Request->post();
        $modelName = $postData['modelid'];
        unset($postData['modelid']);
        $primaryKey = $modelName::$primary_key;
        unset($postData[$primaryKey . '_id']);
        foreach ($postData as $columnName => $val) {
            $column = Text::left($columnName, strlen($columnName) - 3);
            $columnDetails = Model::getColumnInfo($modelName::$table_name, $column);
            $columnComments = $columnDetails[0]['comment'];
            $columnComments = $columnDetails !== null ? json_decode($columnComments, true) : false;
            if ($columnComments && isset($columnComments['required']) && $columnComments == true) {
                if ($val == null) {
                    $this['#loadingdiv']->fadeOut();
                    alert("Field '$column' cannot be Blank");
                    $this['#' . $columnName]->focus();
                    die();
                }
            }
            if ($columnComments && isset($columnComments['validate'])) {
                foreach ($columnComments['validate'] as $rule => $value) {
                    if (is_numeric($rule)) {
                        $rule = $value;
                        $validationErrorArray = array($column);
                    } else {
                        if (!is_array($value)) {
                            $value = array($value);
                        }
                        $validationErrorArray = array_merge(array($column), $value);
                    }
                    if (!is_array($value)) {
                        $value = array($value);
                    }


                    $result = call_user_func_array(array('Validator', $rule), array_merge(array($postData[$columnName]), $value));
                    if (!$result) {
                        $errorMessage = Configure::get('validation.errors.' . $rule);
                        $errorMessage = Text::formatString($errorMessage, $validationErrorArray);
                        alert($errorMessage);
                        $this['#' . $columnName]->focus();
                        $this['#loadingdiv']->fadeOut();
                        die();
                    }
                }
            }
            if ($columnComments && isset($columnComments['type']) && $columnComments['type'] == 'password') {
                $encryption = Configure::get('security.encryption.passwords');
                $val = call_user_func($encryption, $val);
            }
            $data[$column] = $val;
        }

        $model = $modelName::create($data);
        $model->save();
        $id = $model->$primaryKey;
        Cache::clean();

        //alert("A New $modelName has been created and assigned ID '$id'");
        redirect(Router::urlFromName('crud_read', array('modelName' => $modelName)), true);
    }

}