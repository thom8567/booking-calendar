<?php declare(strict_types=1);
/**
 * Plugin Name: Booking Calendar
 */

include 'dbConnection.php';

function getPDOConnection()
{
    $dbConnection = new PDOConnection();
    return $dbConnection->pdo;
}

/*
 * Activate the plugin by creating a new table
 * in the Wordpress DB.
 * This will need a PDO connection to run
 */
function calendar_booker_activate() : void
{
    $pdo = getPDOConnection();
    $createTableSQL = $pdo->prepare("CREATE TABLE calendarEvents (
        id int(11) NOT NULL UNIQUE AUTO_INCREMENT PRIMARY KEY,
        eventName varchar(255) NOT NULL,
        eventStartDate varchar(255) NOT NULL,
        eventDetails JSON
    )");
    $createTableSQL->execute();
}

/*
 * Deactivate the plugin by deleting all data within the
 * table created by the activation function.
 */
function calendar_booker_deactivate() : void
{
    $pdo = getPDOConnection();
    $deleteDataSQL = $pdo->prepare("DELETE FROM calendarEvents");
    $deleteDataSQL->execute();
}

/*
 * Uninstall the plugin by dropping the created table
 */
function calendar_booker_uninstall() : void
{
    $pdo = getPDOConnection();
    $dropTableSQL = $pdo->prepare("DROP TABLE calendarEvents");
    $dropTableSQL->execute();
}