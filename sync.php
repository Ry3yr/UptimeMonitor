<?php

// Array containing server details
$servers = [
    [
        'host' => 'ftp.xyz.de',
        'user' => 'admin@xyz.de',
        'pass' => '', // Replace with actual password
        'remoteDir' => 'PHP/0demo/2025-08-04-UptimeCheck',
        'localDir' => '/storage/emulated/0/pws/www/2025-08-04-UptimeCheck',
        'successMessage' => 'Successfully uploaded to alcea-wisteria.de'
    ],
    [
        'host' => 'ftp..xyz.de',
        'user' => 'root@xyz.de',
        'pass' => '', // Replace with actual password
        'remoteDir' => 'alceawis.de/other/extra/fetchdata/2025-08-04-UptimeCheck',
        'localDir' => '/storage/emulated/0/pws/www/2025-08-04-UptimeCheck',
        'successMessage' => 'Successfully uploaded to alceawis.de'
    ]
];

// Files to be uploaded
$files = [
    'alceawis.com.json',
    'alceawis.de.json',
    'alcea-wisteria.de.json'
];

foreach ($servers as $server) {
    foreach ($files as $file) {
        $localFile = "{$server['localDir']}/$file";
        $remoteFile = "{$server['remoteDir']}/$file";

        // Check if local file exists
        if (!file_exists($localFile)) {
            echo "Local file not found: $localFile\n";
            continue;
        }

        // Open local file
        $fp = fopen($localFile, 'r');
        if (!$fp) {
            echo "Failed to open local file: $localFile\n";
            continue;
        }

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "ftp://{$server['host']}/$remoteFile");
        curl_setopt($ch, CURLOPT_USERPWD, "{$server['user']}:{$server['pass']}");
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFile));
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Optional: shows transfer details

        // Execute the file upload
        $result = curl_exec($ch);
        if (!$result) {
            echo "Upload failed for $file to {$server['host']}: " . curl_error($ch) . "\n";
        } else {
            echo "{$server['successMessage']}: $file\n"; // Added line break here
            echo "\n"; // Line break after success message
        }

        // Clean up
        curl_close($ch);
        fclose($fp);
    }
}

?>
