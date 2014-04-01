<?php

Class Csv
{

    public function read($filePath, $delimiter = ',', $includeFirstRow = false)
    {
        if (!is_file($filePath)) {
            return;
        }
        if (($handle = fopen($filePath, "r")) !== false) {
            $i = 0;
            while (($lineArray = fgetcsv($handle, 4000, $delimiter)) !== false) {
                for ($j = 0; $j < count($lineArray); $j++) {
                    $data2DArray[$i][$j] = str_replace('\\r', '\\n', $lineArray[$j]);
                }
                $i++;
            }
            if (!$includeFirstRow)
                unset($data2DArray[0]);
            fclose($handle);
        }
        return $data2DArray;
    }

}