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


// Map zipcodes to every possible "state-rate_area"
$zip_areas = [];
foreach ($zip_rows as $zip) {
    $zip_areas[$zip['zipcode']][] = $zip['state'] . '-' . $zip['rate_area'];//(will have duplicates)
}


// Put together the output
$output_rows = ["zipcode,rate"];
foreach ($slcsp_rows as $slcsp_row) {
    $zip = $slcsp_row['zipcode'];
    $rate = '';//(default to blank)

    // Determine the rate (if we can)
    $possible_areas = array_unique($zip_areas[$zip] ?? []);
    if (count($possible_areas) === 1) { //<--- only calculate if there's a SINGLE area
        $target_area = $possible_areas[0];

        // If there IS a second lowest rate...
        if (isset($rate_areas[$target_area]) && count($rate_areas[$target_area]) >= 2) {

            // Format and use it.
            $rate = number_format(
                $rate_areas[$target_area][1], 
                2, 
                '.', 
                ''//(no thousands separator since this is a CSV)
            );
        }
    }

    // Always output the row (even if rate is blank)
    $output_rows[] = $zip . ',' . $rate;
}


// Output results
echo implode("\n", $output_rows) . "\n";


// Simple CSV reader that returns an array of associative arrays
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