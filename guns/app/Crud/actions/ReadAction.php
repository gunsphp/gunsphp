<?php

class ReadAction extends CrudAppController
{

    public function main($modelName)
    {
        $columns = Model::getColumnInfo($modelName::$table_name);
        $displayCols = array();
        foreach ($columns as $column) {
            $colComments = strlen($column['comment']) > 0 ? json_decode($column['comment'], true) : false;
            if ($colComments !== false && isset($colComments['display']) && $colComments['display'] == true) {
                $displayCols1[] = "`" . $column['field'] . "`";
                $displayCols[] = $column['field'];
            }
        }
        $this->set('displayColumns', $displayCols);
        if (count($displayCols1) > 0) {
            $options['select'] = implode(', ', $displayCols1);
            $result = $modelName::all($options);
            $result = Model::result_array($result);
            $this->set('data', $result);
        } else {
            $this->set('data', false);
        }
        $this->set('tableName', $modelName::$table_name);
        $this->set('modelName', $modelName);
        $this->set('primaryKey', $modelName::$primary_key);
        $this->set('post', array('tableid_click_ajax' => array('modelName' => $modelName)));
        $this->set('classEvents', array('deleteEntry_onClick'));
    }

    public function deleteEntry_onClick()
    {
        $this['this']->getAttribute('dataid', '$dataid');
        $this['#modelName']->getValue('$modelName');
        $ajaxCall = callback('ajax', array(
            'Crud/delete?call=deleteData',
            array(
                'data' => array(
                    'dataid' => '$dataid',
                    'modelName' => '$modelName'
                ))
        ));
        iif('confirm("Are you sure you want to delete this item?")', $ajaxCall);
    }

} 