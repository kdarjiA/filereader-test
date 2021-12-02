<?php

class Xml implements FileReader
{
    public function readData($file)
    {
        // read xml and convert to array
        $xmlData = file_get_contents($file);
        $xml = $this->emptyNodeToNull(simplexml_load_string($xmlData, "SimpleXMLElement", LIBXML_NOCDATA));
        $json = json_encode($xml);
        $file_data = json_decode($json, TRUE);

        if (!isset($file_data['row'])) {
            print 'File data not Found.';
            return false;
        }
        try {
            foreach ($file_data['row'] as $record) {
                RowOperations::process($record);
            }
        } catch (Exception $e) {
            print $e->getMessage();;
            return false;
        }

        return true;
    }


    // function for assign null value for empty xml node
    public function emptyNodeToNull($data)
    {
        if ($data instanceof \SimpleXMLElement and $data->count() === 0) {
            return null;
        }
        $data = (array)$data;
        foreach ($data as &$value) {
            if (is_array($value) or $value instanceof \SimpleXMLElement) {
                $value = $this->emptyNodeToNull($value);
            }
        }
        return $data;
    }
}