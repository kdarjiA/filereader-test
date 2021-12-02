<?php

include 'includes.php';

// global variables
$GLOBALS['header'] = ['make', 'model', 'condition', 'grade', 'capacity', 'colour', 'network'];
$GLOBALS['hashes'] = [];
$GLOBALS['values'] = [];
$GLOBALS['column_size'] = sizeof($GLOBALS['header']);

// get options
$options = getopt(null, ["file:", "unique-combinations::"]);

// check file parameter
if (!isset($options['file'])) {
    print 'File parameter must be required.';
    return false;
}

$file = $options['file'];
// check if file exists
if (!isFileExists($file)) {
    print 'File not found.';
    return false;
}

// get an extension of file
$extension = pathinfo($file, PATHINFO_EXTENSION);
if (!isExtensionAllowed($extension)) {
    print 'File extension must be csv,json or xml.';
    return false;
}

// call class by extensions
$class = getClassName($extension);
$type = new $class();

if ($type->readData($file)) {
    // output file
    $output_file = isset($options['unique-combinations']) ? $options['unique-combinations'] : 'combination_count.csv';

    // call function for put data into csv
    $csv = Csv::putData($output_file, $GLOBALS['header'], $GLOBALS['values']);
}
?>