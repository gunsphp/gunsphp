<?php

class Validator
{

    public function isEmail($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_EMAIL);
        return $result;
    }

    public function isBoolean($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_BOOLEAN);
        return $result;
    }

    public function minLength($string, $length)
    {
        if (strlen($string) >= $length) {
            return true;
        } else {
            return false;
        }
    }

    public function maxLength($string, $length)
    {
        if (strlen($string) <= $length) {
            return true;
        } else {
            return false;
        }
    }

    public function isIp($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_IP);
        return $result;
    }

    public function isUrl($string)
    {
        $result = filter_var($string, FILTER_VALIDATE_URL);
        return $result;
    }

    public function isNumeric($string)
    {
        return is_numeric($string);
    }

    public function validateFromModel($data, $tableName)
    {
        $columns = Cache::get($tableName . "_columns");
        if (!$columns) {
            $columns = Model::getColumnInfo($tableName);
            Cache::set($tableName . "_columns");
        }
        $result = array();
        //$columns = json_decode($columns, true);
        foreach ($data as $d) {
            $proceed = true;
            foreach ($d as $fieldName => $fieldValue) {
                $colComments = false;
                foreach ($columns as $col) {
                    if ($col['field'] == $fieldName) {
                        $colComments = $col['comment'];
                        if (strlen($colComments) > 0) {
                            $colComments = json_decode($colComments, true);
                            $colComments = $colComments['validate'];
                            if (count($colComments) == 0) {
                                $colComments = false;
                            }
                        } else {
                            $colComments = false;
                        }

                        if (!$colComments == false) {
                            foreach ($colComments as $rule => $ruleValue) {
                                $arguments = array_merge(array($fieldValue), array($ruleValue));
                                $return = call_user_func_array(array($this, $rule), $arguments);
                                if ($return == false) {
                                    $result['errors'][] = array(
                                        'rule' => $rule,
                                        'key' => $fieldName,
                                        'value' => $fieldValue,
                                        'message' => Text::formatString(Configure::get('validation.errors.' . $rule), $arguments),
                                        'data' => $d
                                    );
                                    $proceed = false;
                                    break;
                                }
                            }
                            if ($proceed == false)
                                break;
                        }
                    }
                    if ($proceed == false)
                        break;
                }
                if ($proceed == false)
                    break;
            }
            if ($proceed) {
                $result['success'][] = $d;
            }
        }
        if (count($result) == 0) {
            return true;
        } else {
            return $result;
        }
    }
} 