<?php

ini_set('max_execution_time', 0);
ini_set('memory_limit',-1);

// global variables
$GLOBALS['header'] = ['make','model','condition','grade','capacity','colour','network'];
$GLOBALS['hashes'] = [];
$GLOBALS['values'] = [];
$GLOBALS['column_size'] = sizeof($GLOBALS['header']);

// function for processing each row
function row_process($row){
    // select columns equals to header size
    $row = array_slice($row,0, $GLOBALS['column_size']);

    // hash row
    $hash = md5(serialize($row));

    if (!isset($GLOBALS['hashes'][$hash])) {
        $GLOBALS['hashes'][$hash] = true;

        // assign header keys to values
        $record = array_combine($GLOBALS['header'], $row);
        var_dump($record);

        // required field
        if(empty($record['make']) || empty($record['model'])){
            //throw new Exception('Model name and brand name must be required.');
        }

        // set count
        $record['count'] = 1;
        $GLOBALS['values'][$hash] = $record;
    }else{
        $count = $GLOBALS['values'][$hash]['count'];

        // unset count for printing data
        unset($GLOBALS['values'][$hash]['count']);
        var_dump($GLOBALS['values'][$hash]);

        // increment count
        $GLOBALS['values'][$hash]['count'] = $count + 1;
    }
}

// function for reading xml
function read_xml($file){
    // read xml and convert to array
    $xmlData = file_get_contents($file);
    $xml = empty_node_to_null(simplexml_load_string($xmlData, "SimpleXMLElement", LIBXML_NOCDATA));
    $json = json_encode($xml);
    $file_data = json_decode($json,TRUE);

    if(!isset($file_data['row'])){
        print 'File data not Found.';
        return false;
    }
    try {
        foreach ($file_data['row'] as $record){
            row_process($record);
        }
    }catch (Exception $e){
        print $e->getMessage();;
        return false;
    }

    return true;
}

// function for reading json
function read_json($file){
    $json = file_get_contents($file);

    // Converts it into a PHP object
    $file_data = json_decode($json);
    if (!$file_data) {
        print 'File data not Found.';
        return false;
    }

    try {
        foreach ($file_data as $record) {
            row_process((array)$record);
        }
    }catch (Exception $e){
        print $e->getMessage();;
        return false;
    }

    return true;
}

// function for reading csv file
function read_csv($file, $length = 1000, $delimiter = ','){

    // open file read mode
    $file_data = fopen($file, 'r');
    if (!$file_data) {
        print 'File data not Found.';
        return false;
    }

    // flag for skipping first record
    $flag = true;
    try{
        while (($row = fgetcsv($file_data, $length, $delimiter)) !== false) {
            if($flag) { $flag = false; continue; }
            row_process($row);
        }
    }catch (Exception $e){
        print $e->getMessage();;
        return false;
    }

    fclose($file_data);
    return true;
}

// function for putting output records to csv
function put_csv($file_name){
    // last column as count as header
    array_push($GLOBALS['header'],'count');
    $GLOBALS['values'] = array_values($GLOBALS['values']);

    // set new header
    array_unshift($GLOBALS['values'], $GLOBALS['header']);
    $records = $GLOBALS['values'];

    // file open written mode
    $fp = fopen($file_name, 'w');

    // row wise put data
    foreach ($records as $fields) {
        // put data into csv
        fputcsv($fp, $fields);
    }

    // close file
    fclose($fp);
    print 'File imported successfully!';
}


// function for assign null value for empty xml node
function empty_node_to_null($data)
{
    if ($data instanceof \SimpleXMLElement and $data->count() === 0) {
        return null;
    }
    $data = (array)$data;
    foreach ($data as &$value) {
        if (is_array($value) or $value instanceof \SimpleXMLElement) {
            $value = empty_node_to_null($value);
        }
    }
    return $data;
}

// get options
$options = getopt(null,["file:","unique-combinations::"]);

if(isset($options['file'])){

    $file = $options['file'];
    // check if file exists
    if (!file_exists($file) ) {
        print 'File not found.';
        return false;
    }

    $output = null;
    // get an extension of file
    $extension = pathinfo($file, PATHINFO_EXTENSION);

    if($extension == 'csv'){
        // call read csv function
        $output = read_csv($file);
    }else if($extension == 'json'){
        // call read json function
        $output = read_json($file);
    }else if($extension == 'xml'){
        // call read xml function
        $output = read_xml($file);
    }

    if($output){
        // output file
        $output_file = isset($options['unique-combinations']) ? $options['unique-combinations'] : 'combination_count.csv';
        // call function for put data into csv
        put_csv($output_file);
    }
}else{
    print 'File parameter must be required.';
    return false;
}
?>
