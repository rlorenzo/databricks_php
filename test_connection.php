<?php
/**
 * Test script to connect to Databricks SQL endpoint from PHP.
 */

require(__DIR__ . '/config.php');

$dsn = 'Databricks';
$user = 'token';
// NOTE $password is defined in config.php.

// First try to connect via ODBC.
echo "Trying ODBC connection...\n";
$odbc = odbc_connect($dsn, $user, $password);
// Checking connection id or reference
if (!$odbc) {
    echo "ODBC connect failed: " . odbc_error() . "\n";
} else {
    echo "ODBC connection successful!\n";
    // NOTE: The below should be replaced with a valid SELECT query.
    $sql = 'SELECT...';
    $resource = odbc_exec($odbc, $sql);
    if ($resource) {
        while ($result = odbc_fetch_array($resource)) {
            print_r($result);
        }
    }
    // Resource releasing
    odbc_close($odbc);
}

echo "DONE!\n";
