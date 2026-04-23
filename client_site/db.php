<?php
/*
    File: db.php 
    Author: Isaac Crft
    Date: March 25, 2026
    Description: Creates a PDO connection to the isaaccra_clientsite
                 MySQL database hosted on HostGator via cPanel.
                 Include this file at the top of any PHP page that
                 needs database access using require_once 'db.php'.
                 Uses a try-catch block so connection errors never
                 expose credentials or sensitive details to the visitor.
*/

// =====================================================================
// CONNECTION PARAMETERS
// =====================================================================

// $type: the database driver — always "mysql" for MySQL databases
$type = "mysql";

// $server: the IP address from Remote MySQL → Manage Access Hosts → %
$server = "192.185.2.183";

// $db: yourcPanelusername_databasename
$db = "isaaccra_clientsite";

// $port: default MySQL port for HostGator
$port = 3306;

// $charset: ensures special characters are stored and retrieved correctly
$charset = "utf8mb4";

// $username: the database user created in cPanel → MySQL Databases
$username = "isaaccra_isaac";

// $password: the password set for the database user in cPanel
$password = "Armando123!@%";

// =====================================================================
// PDO OPTIONS
// Passed to the PDO constructor to control how it behaves.
// =====================================================================
$options = [
    // Throw a PDOException whenever PDO encounters an error
    // (instead of silently failing or returning false)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // Return each row from a result set as an associative array
    // e.g. $row['trip_name'] instead of $row[0]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Disable emulated prepares so PDO returns integers as integers
    // (not as strings), matching the actual column data types
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// =====================================================================
// DSN (Data Source Name)
// Combines the connection parameters into the format PDO expects.
// =====================================================================
$dsn = "$type:host=$server;port=$port;dbname=$db;charset=$charset";

// =====================================================================
// PDO OBJECT CREATION
// The try block attempts to connect. If it succeeds, the PDO object
// is stored in $pdo and available to any file that includes this one.
// If it fails, PDO throws a PDOException — caught below and re-thrown
// so the error is logged server-side without exposing credentials.
// =====================================================================
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Re-throw the exception with the message and code intact.
    // This prevents raw connection details from appearing in the browser
    // while still allowing the server error log to capture the issue.
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
