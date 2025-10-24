<?php
// Determine the "Second Lowest Cost Silver Plan" (SLCSP) for a list of ZIP codes.
//
//      Usage: php slcsp.php
//

$plan_rows  = readCsv('./input/plans[76].csv');
$zip_rows   = readCsv('./input/zips[62].csv');
$slcsp_rows = readCsv('./input/slcsp[20].csv');

echo print_r($slcsp_rows, true);


function readCsv($file) {
    $rows = [];
    if (($handle = fopen($file, 'r')) !== false) {
        $headers = fgetcsv($handle, 0, ',', '"', '\\');
        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $rows[] = array_combine($headers, $data);
        }
        fclose($handle);
    }
    return $rows;
}