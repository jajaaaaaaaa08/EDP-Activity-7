<?php
/**
 * AUTO-INSTALLER FOR PHPSPREADSHEET
 * This script bypasses SSL issues to install the required library.
 */
echo "<h3>PhpSpreadsheet Auto-Installer</h3>";

if (file_exists('vendor/autoload.php')) {
    die("<p style='color:green;'>Library is already installed! You can now use the Export Excel buttons.</p>");
}

// 1. Download composer.phar if missing
if (!file_exists('composer.phar')) {
    echo "Downloading composer.phar...<br>";
    $url = "https://getcomposer.org/composer.phar";
    $fp = fopen('composer.phar', 'w+');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // BYPASS SSL
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}

// 2. Run install command
echo "Installing PhpSpreadsheet (this may take a minute)...<br>";
$cmd = "C:\\xampp\\php\\php.exe composer.phar require phpoffice/phpspreadsheet --ignore-platform-reqs 2>&1";
$output = shell_exec($cmd);

echo "<pre>$output</pre>";

if (file_exists('vendor/autoload.php')) {
    echo "<p style='color:green; font-weight:bold;'>SUCCESS! Library installed. You can now close this page and export your reports.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>FAILED. Please check the output above.</p>";
}
