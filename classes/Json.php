<?php

class Json implements FileReader
{

    public function readData($file)
    {
        $json = file_get_contents($file);

        // Converts it into a PHP object
        $file_data = json_decode($json);
        if (!$file_data) {
            print 'File data not Found.';
            return false;
        }

        try {
            foreach ($file_data as $record) {
                RowOperations::process((array)$record);
            }
        } catch (Exception $e) {
            print $e->getMessage();;
            return false;
        }

        return true;
    }
}