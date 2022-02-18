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
    // Resource releasing
    odbc_close($odbc);
}

// Next try PDO.
try {
    echo "\n\nTrying PDO connection...\n";
    $dsn = 'odbc:Databricks';
    $pdo = new PDO($dsn, $user, $password);
    echo "PDO connection successful!\n";
} catch (Exception $e) {
    echo "PDO connection failed: " . $e->getMessage() . "\n";
}

echo "DONE!\n";
