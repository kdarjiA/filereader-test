<?php

class Csv implements FileReader
{
    const LENGTH = 1000;
    const DELIMITER = ',';

    public function readData($file)
    {
        // open file read mode
        $file_data = fopen($file, 'r');
        if (!$file_data) {
            print 'File data not Found.';
            return false;
        }

        // flag for skipping first record
        $flag = true;
        try {
            while (($row = fgetcsv($file_data, self::LENGTH, self::DELIMITER)) !== false) {
                if ($flag) {
                    $flag = false;
                    continue;
                }
                RowOperations::process($row);
            }
        } catch (Exception $e) {
            print $e->getMessage();
            return false;
        }

        fclose($file_data);
        return true;
    }

    public static function putData($file_name, $headers, $values)
    {
        // last column as count as header
        array_push($headers, 'count');
        $values = array_values($values);

        // set new header
        array_unshift($values, $headers);
        $records = $values;

        // file open written mode
        $fp = fopen($file_name, 'w');

        try {
            // row wise put data
            foreach ($records as $fields) {
                // put data into csv
                fputcsv($fp, $fields);
            }
        } catch (Exception $e) {
            print $e->getMessage();;
            return false;
        }

        // close file
        fclose($fp);
        print 'File imported successfully!';
        return true;
    }
}