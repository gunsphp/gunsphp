<?php


class UpdateAction extends CrudAppController
{

    public function main($modelName, $id)
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
        $this->set('id', $id);
        $this->set('modelName', $modelName);
        $this->set('post', array(
            'onload' => array(
                'modelName' => $modelName,
                'id' => $id,
                'fields' => $fields
            )
        ));
    }

    public function onload()
    {
        $modelName = $this->Request->post('modelName');
        $id = $this->Request->post('id');
        $fields = $this->Request->post('fields');
        try {
            $data = $modelName::find($id);
            $data = Model::result_array($data);
            $data = $data[0];
        } catch (\ActiveRecord\RecordNotFound $exception) {
            alert("Invalid ID: $id");
            $this['#loadingdiv']->setHtml('No Records Found with ID : ' . $id);
            die();
        }

        foreach ($fields as $field) {
            $compliedFields[$field['name']] = $field;
            unset($compliedFields[$field['name']]['name']);
        }

        foreach ($data as $colName => $d) {
            $fieldId = '#' . $colName . '_id';
            $this[$fieldId]->setValue($d);
        }

        $this['#loadingdiv']->fadeOut();
    }

    function updateform_onSubmit()
    {
        $this['#loadingdiv']
            ->setHtml('Updating Records. Please wait...')
            ->fadeIn();
        ajax('crud/update?call=edit_form', array(
            'data' => callback('serialize_form', array('#updateform'))
        ));
        parseScript('return false;');
    }

    function edit_form()
    {
        $postData = $this->Request->post();
        $modelName = $postData['modelid'];
        $id = $postData['primaryid'];
        $model = $modelName::find($id);
        unset($postData['modelid']);
        unset($postData['primaryid']);
        $columns = Model::getColumnInfo($modelName::$table_name);
        foreach ($columns as $key => $column) {
            $columnComments = strlen($column['comment']) > 0 ? json_decode($column['comment'], true) : false;
            $f = array();
            if ($columnComments !== false) {
                $f['name'] = $column['field'];
                $f['type'] = isset($columnComments['type']) ? $columnComments['type'] : 'text';
                unset($columnComments['type']);
                $f = array_merge($f, $columnComments);
                $fields[$column['field']] = $f;
            }
        }

        foreach ($postData as $key => $value) {
            $k = Text::left($key, strlen($key) - 3);
            if (!isset($fields[$key]['readonly']) || $fields[$key]['readonly'] !== true) {
                $model->$k = $value;
            }
        }
        $model->save();
        Cache::clean();
        //alert("$modelName with id $id has been updated successfully!");
        redirect(Router::urlFromName('crud_read', array('modelName' => $modelName)), true);
    }

} 