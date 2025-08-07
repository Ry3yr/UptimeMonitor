<?php
$host = 'ftp.fileserve.com';
$user = '';
$pass = '';
$remoteDir = 'PHP/0demo/2025-08-04-UptimeCheck';
$localDir = '/storage/emulated/0/pws/www/2025-08-04-UptimeCheck';

$files = [
    'alceawis.com.json',
    'alceawis.de.json',
    'alcea-wisteria.de.json'
];

foreach ($files as $file) {
    $localFile = "$localDir/$file";
    $remoteFile = "$remoteDir/$file";

    if (!file_exists($localFile)) {
        echo "Local file not found: $localFile\n";
        continue;
    }

    $fp = fopen($localFile, 'r');
    if (!$fp) {
        echo "Failed to open local file: $localFile\n";
        continue;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "ftp://$host/$remoteFile");
    curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
    curl_setopt($ch, CURLOPT_UPLOAD, true);
    curl_setopt($ch, CURLOPT_INFILE, $fp);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFile));
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Optional: shows transfer details

    $result = curl_exec($ch);
    if (!$result) {
        echo "Upload failed for $file: " . curl_error($ch) . "\n";
    } else {
        echo "Successfully uploaded: $file\n";
    }

    curl_close($ch);
    fclose($fp);
}
?>
