<?php
// Determine the "Second Lowest Cost Silver Plan" (SLCSP) for a list of ZIP codes.
// Usage:  php slcsp.php


// First, let's just read everything in.
$plan_rows  = readCsv('./input/plans[76].csv');
$zip_rows   = readCsv('./input/zips[62].csv');
$slcsp_rows = readCsv('./input/slcsp[20].csv');

// Fetch only the SILVER plan rates, grouped as "state-rate_area"
$rate_areas = [];
foreach ($plan_rows as $cur_plan) {
    if ($cur_plan['metal_level'] === 'Silver') {
        $key = $cur_plan['state'] . '-' . $cur_plan['rate_area'];
        $rate_areas[$key][] = floatval($cur_plan['rate']);
    }
}

// Remove "ties" and sort
foreach ($rate_areas as $key => &$rates) {
    $rates = array_unique($rates);
    sort($rates, SORT_NUMERIC);
}
unset($rates);

echo print_r($rate_areas, true);


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