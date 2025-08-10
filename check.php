<?php
$domains = [
    'alceawis.de' => ['mode' => 'response_code'],
    'alceawis.com' => ['mode' => 'file_check'],
    'alcea-wisteria.de' => ['mode' => 'response_code']
];

// cURL request with timeout and response code check
function curlGetResponseCode($url, $timeout = 30) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error       = curl_errno($ch);
    curl_close($ch);

    // Timeout or other error? Treat as 404
    if ($error) {
        return 404;
    }

    return $responseCode ?: 404;
}

function checkFileExistence($url, $filename) {
    $fullUrl = $url . '/' . $filename;
    $responseCode = curlGetResponseCode($fullUrl, 30);
    return $responseCode === 200;
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
        $responseData['response_code'] = curlGetResponseCode("https://$domain", 30);
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
