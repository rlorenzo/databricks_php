<?php
/**
 * Test script to connect to Databricks SQL endpoint from PHP.
 */

require(__DIR__ . '/config.php');

$dsn = 'Databricks';
$user = 'token';
// NOTE $password is defined in config.php.

// Try to connect via ODBC.
echo "Trying ODBC connection...\n";
$odbc = odbc_connect($dsn, $user, $password);
// Checking connection id or reference
if (!$odbc) {
    echo "ODBC connect failed: " . odbc_error() . "\n";
} else {
    echo "ODBC connection successful!\n";

    $sql = 'SHOW DATABASES';
    echo 'Query: ' . $sql . "\n";
    $results = odbc_exec($odbc, $sql);
    while (odbc_fetch_row($results)) {
        for ($i=1; $i<=odbc_num_fields($results); $i++) {
            echo odbc_result($results, $i) . "\n";
        }
    }

    // Resource releasing
    odbc_close($odbc);
}

// Next try PDO.
try {
    echo "\nTrying PDO connection...\n";
    $dsn = 'odbc:Databricks';
    $pdo = new PDO($dsn, $user, $password);
    echo "PDO connection successful!\n";

    $sql = 'SHOW DATABASES';
    echo 'Query: ' . $sql . "\n";
    $results = $pdo->query($sql);
    foreach ($results as $row) {
        echo $row[0] . "\n";
    }

} catch (Exception $e) {
    echo "PDO connection failed: " . $e->getMessage() . "\n";
}


echo "\nDONE!\n";
