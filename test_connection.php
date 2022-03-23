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

    // Try query with date and prepared statements.
    echo "1. Try query with date and prepared statements.\n";
    $sql = 'SELECT count(1), companyid, company_name as name, series, investment_date
    FROM refactored_db.view_afd
    WHERE companyid not in (
        SELECT companyid
            FROM refactored_db.view_afd
            WHERE investment_date < DATE(NOW()-INTERVAL 64 DAY))
    AND investment_date BETWEEN DATE(NOW()-INTERVAL 64 DAY) AND DATE(NOW())
    GROUP BY companyid, company_name, series, investment_date
    HAVING count(1) <= ?';
    echo 'Query: ' . $sql . "\n";
    /**
     * This returns the following error:
     * Warning: odbc_prepare(): SQL error: [Simba][Hardy] (80) Syntax or semantic
     * analysis error thrown in server while executing query. Error message from
     * server: org.apache.hive.service.cli.HiveSQLException: Error running query:
     * org.apache.spark.sql.catalyst.parser.ParseException:
     * mismatched input '?' expecting {'(', '{', 'APPLY', 'CALLED', 'CHANGES',
     * 'CLONE', 'COLLECT', 'CONTAINS', 'CONVERT', 'COPY', 'COPY_OPTIO, SQL state
     * 37000 in SQLPrepare in /test_connection.php on line 31
     */
    $res = odbc_prepare($odbc, $sql);
    if (!$res) {
        echo 'odbc_prepare Error: ' . odbc_errormsg() . "\n";
    } else {
        if (!odbc_execute($res, [5])) {
            echo 'odbc_execute Error: ' . odbc_errormsg() . "\n";
        } else {
            echo "Showing results\n";
            while ($row = odbc_fetch_array($res)) {
                echo 'Row: ' . print_r($row, true) . "\n";
            }
        }
    }

    // Try query without date and prepared statements.
    echo "2. Try query without date and prepared statements.\n";
    $sql = 'SELECT count(1), companyid, company_name as name, series, investment_date
    FROM refactored_db.view_afd
    WHERE companyid not in (
        SELECT companyid
            FROM refactored_db.view_afd
        )
    GROUP BY companyid, company_name, series, investment_date
    HAVING count(1) <= ?';
    echo 'Query: ' . $sql . "\n";
    /**
     * This works but returns no results because we need the date.
     */
    $res = odbc_prepare($odbc, $sql);
    if (!$res) {
        echo 'odbc_prepare Error: ' . odbc_errormsg() . "\n";
    } else {
        if (!odbc_execute($res, [5])) {
            echo 'odbc_execute Error: ' . odbc_errormsg() . "\n";
        } else {
            echo "Showing results\n";
            while ($row = odbc_fetch_array($res)) {
                echo 'Row: ' . print_r($row, true) . "\n";
            }
        }
    }

    // Try query with date and no prepare statement.
    echo "3. Try query with date and no prepare statement.\n";
    $sql = 'SELECT count(1), companyid, company_name as name, series, investment_date
    FROM refactored_db.view_afd
    WHERE companyid not in (
        SELECT companyid
            FROM refactored_db.view_afd
            WHERE investment_date < DATE(NOW()-INTERVAL 64 DAY))
    AND investment_date BETWEEN DATE(NOW()-INTERVAL 64 DAY) AND DATE(NOW())
    GROUP BY companyid, company_name, series, investment_date
    HAVING count(1) <= 5';
    echo 'Query: ' . $sql . "\n";
    $results = odbc_exec($odbc, $sql);
    echo "Showing results\n";
    while ($row = odbc_fetch_array($results)) {
        echo 'Row: ' . print_r($row, true) . "\n";
    }

    // Resource releasing
    odbc_close($odbc);
}

// // Next try PDO.
// try {
//     echo "\nTrying PDO connection...\n";
//     $dsn = 'odbc:Databricks';
//     $pdo = new PDO($dsn, $user, $password);
//     echo "PDO connection successful!\n";

//     $sql = 'SHOW DATABASES';
//     echo 'Query: ' . $sql . "\n";
//     $results = $pdo->query($sql);
//     foreach ($results as $row) {
//         echo $row[0] . "\n";
//     }

// } catch (Exception $e) {
//     echo "PDO connection failed: " . $e->getMessage() . "\n";
// }


echo "\nDONE!\n";
