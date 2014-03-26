<?php

class ConfigureAction extends CrudAppController
{

    public function main($modelName)
    {
        $this->set('modelName', $modelName);
        $this->set('tableName', $modelName::$table_name);
        $fields = Model::getColumnInfo($modelName::$table_name);
        $this->set('fields', $fields);
        $this->set('post', array('onload' => array('modelName' => $modelName, 'tableName' => $modelName::$table_name)));
    }

    public function submitchanges_onClick()
    {
        $this['#submitchanges']->addClass('disabled')->setHtml('Submitting Changes...');
        getValue('#modelName', '$modelName');
        getValue('#tableName', '$tableName');
        ajax('crud/configure?call=submit', array('data' => array('modelName' => '$modelName', 'tableName' => '$tableName')));
        return_false();
    }

    public function submit()
    {
        $modelName = $this->Request->post('modelName');
        $tableName = $this->Request->post('tableName');
        $columns = Model::getColumnInfo($tableName);
        //e($columns);
        $columnComments = array();
        foreach ($columns as $column) {
            $columnName = $column['field'];

            $this['[class="isprimary"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_primary_sys');
            $columnComments[$columnName]['primary'] = '$' . $columnName . '_primary_sys';

            $this['[class="display"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_display_sys');
            $columnComments[$columnName]['display'] = '$' . $columnName . '_display_sys';

            $this['[class="required"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_required_sys');
            $columnComments[$columnName]['required'] = '$' . $columnName . '_required_sys';

            $this['[class="readonly"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_readonly_sys');
            $columnComments[$columnName]['readonly'] = '$' . $columnName . '_readonly_sys';

            $this['[class="autoload"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_autoload_sys');
            $columnComments[$columnName]['autoload'] = '$' . $columnName . '_autoload_sys';

            $this['[class="datatype"][fieldname="' . $columnName . '"]']->getValue('$' . $columnName . '_datatype_sys');
            $columnComments[$columnName]['type'] = '$' . $columnName . '_datatype_sys';

            $this['[class="foreignkey"][fieldname="' . $columnName . '"]']->getValue('$' . $columnName . '_foreignkey_sys');
            $columnComments[$columnName]['foreign'] = '$' . $columnName . '_foreignkey_sys';

            $this['[class="validation isEmail"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_isEmail_sys');
            $columnComments[$columnName]['validate']['isEmail'] = '$' . $columnName . '_isEmail_sys';

            $this['[class="validation isBoolean"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_isBoolean_sys');
            $columnComments[$columnName]['validate']['isBoolean'] = '$' . $columnName . '_isBoolean_sys';

            $this['[class="validation isIp"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_isIp_sys');
            $columnComments[$columnName]['validate']['isIp'] = '$' . $columnName . '_isIp_sys';

            $this['[class="validation isUrl"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_isUrl_sys');
            $columnComments[$columnName]['validate']['isUrl'] = '$' . $columnName . '_isUrl_sys';

            $this['[class="validation isNumeric"][fieldname="' . $columnName . '"]']->is(':checked', '$' . $columnName . '_isNumeric_sys');
            $columnComments[$columnName]['validate']['isNumeric'] = '$' . $columnName . '_isNumeric_sys';

            $this['[class="validation minLength"][fieldname="' . $columnName . '"]']->getValue('$' . $columnName . '_minLength_sys');
            $columnComments[$columnName]['validate']['minLength'] = '$' . $columnName . '_minLength_sys';

            $this['[class="validation maxLength"][fieldname="' . $columnName . '"]']->getValue('$' . $columnName . '_maxLength_sys');
            $columnComments[$columnName]['validate']['maxLength'] = '$' . $columnName . '_maxLength_sys';

            $this['[class="comments"][fieldname="' . $columnName . '"]']->getValue('$' . $columnName . '_comments_sys');
            $columnComments[$columnName]['comment'] = '$' . $columnName . '_comments_sys';
        }

        $columnComments['otherinfo144003'] = array('modelName' => $modelName);

        ajax('crud/configure?call=submitconfirm', array(
            'data' => $columnComments,
            'error' => 'alert(jqXHR);'
        ));

        //redirect(Router::urlFromName('crud_configure', array('modelName' => $modelName)), true);
    }

    public function submitconfirm()
    {
        $postData = $this->Request->post();
        $modelName = $postData['otherinfo144003']['modelName'];
        unset($postData['otherinfo144003']);
        $tableName = $modelName::$table_name;
        $columns = Model::getColumnInfo($tableName);
        //e($columns);
        foreach ($columns as $column) {
            $comment = $postData[$column['field']];
            foreach ($comment as $key => $value) {
                if ($value == false || $value == '' || $value == 'false') {
                    unset($comment[$key]);
                }
                if ($value == 'true') {
                    $comment[$key] = true;
                }
            }
            foreach ($comment['validate'] as $key => $value) {
                if ($value == false || $value == '' || $value == 'false') {
                    unset($comment['validate'][$key]);
                }
                if ($value == 'true') {
                    $comment['validate'][$key] = true;
                }
            }
            $comment = json_encode($comment);
            $columnName = $column['field'];
            $columnAttributes = $column['type'] . ' ';
            $columnAttributes .= ($column['null'] == 'YES') ? 'NULL ' : 'NOT NULL ';
            if ($column['key'] == 'PRI') {
                $columnAttributes .= 'AUTO_INCREMENT ';
            } else {
                if ($column['null'] == 'NO' && $column['default'] == null) {

                } else {
                    $columnAttributes .= "DEFAULT " . (($column['default'] == null) ? "NULL" : "'" . $column['default'] . "'") . ' ';
                }
            }
            $comment = stripcslashes($comment);
            $comment = str_replace('"', '\\"', $comment);
            $sql = "ALTER TABLE `$tableName` MODIFY COLUMN `$columnName` $columnAttributes COMMENT '$comment'";
            //e($sql);
            //e("\n\n");
            try {
                $modelName::query($sql);
            } catch (\ActiveRecord\ActiveRecordException $e) {
                $error = (array)$e;
                $str = "<strong>Error Processing Request:</strong><hr/>";
                foreach ($error as $k => $err) {
                    $str .= $err . "<hr/>";
                    unset($error[$key]);
                    break;
                }
                //$str .= encode($error['Exceptiontrace']);

                $this['#errorDetails']->setHtml("<pre>" . print_r($error, true) . "</pre>");
                $this['#errorMessage']->setHtml($str);
                $this['#submitchanges']->removeClass('disabled')->setHtml('Submit Changes');
                die();
            }
        }
        alert('All Changes have been applied Successfully!');
        redirect(Router::urlFromName('crud_listall'), true);
        $this['#submitchanges']->removeClass('disabled')->setHtml('Submit Changes');
    }
}