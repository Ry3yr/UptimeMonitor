<?php
$domains = [
    'alceawis.de' => ['mode' => 'response_code'],  // Mode 2: Response Code Check
    'alceawis.com' => ['mode' => 'file_check'],    // Mode 1: File Existence Check
    'alcea-wisteria.de' => ['mode' => 'response_code'] // Mode 2: Response Code Check
];
function checkFileExistence($url, $filename) {
    $headers = @get_headers($url . '/' . $filename, 1);
    return isset($headers[0]) && strpos($headers[0], '200') !== false;
}
function checkResponseCode($url) {
    $headers = @get_headers($url, 1);
    $responseCode = isset($headers[0]) ? (int)substr($headers[0], 9, 3) : 0;
    return $responseCode;
}
foreach ($domains as $domain => $config) {
    $date = date('Y-m-d H:i:s');
    $mode = $config['mode'];
    $responseData = [
        'date' => $date,
        'mode' => $mode,
        'domainname' => $domain,
        'response_code' => 0
    ];
    if ($mode === 'file_check') {
        $exists = checkFileExistence("https://$domain", 'favicon.png') ||
                  checkFileExistence("https://$domain", 'index.html');
        $responseData['response_code'] = $exists ? 200 : 404;
    }
    if ($mode === 'response_code') {
        $responseData['response_code'] = checkResponseCode("https://$domain");
    }
    $filename = "$domain.json";
    $existingData = [];
    if (file_exists($filename)) {
        $json = file_get_contents($filename);
        $decoded = json_decode($json, true);
        if (is_array($decoded) && array_keys($decoded) === range(0, count($decoded) - 1)) {
            $existingData = $decoded;
        }
    }
    $existingData[] = $responseData;
    file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));
    echo "Test result for $domain saved/updated in $filename\n";
}
?>
