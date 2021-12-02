<?php

class RowOperations
{
    public static function process($row)
    {
        // select columns equals to header size
        $row = array_slice($row, 0, $GLOBALS['column_size']);

        // hash row
        $hash = md5(serialize($row));

        if (!isset($GLOBALS['hashes'][$hash])) {
            $GLOBALS['hashes'][$hash] = true;

            // assign header keys to values
            $record = array_combine($GLOBALS['header'], $row);
            var_dump($record);

            // required field
            if (empty($record['make']) || empty($record['model'])) {
                throw new Exception('Model name and brand name must be required.');
            }

            // set count
            $record['count'] = 1;
            $GLOBALS['values'][$hash] = $record;
        } else {
            $count = $GLOBALS['values'][$hash]['count'];

            // unset count for printing data
            unset($GLOBALS['values'][$hash]['count']);
            var_dump($GLOBALS['values'][$hash]);

            // increment count
            $GLOBALS['values'][$hash]['count'] = $count + 1;
        }
    }
}