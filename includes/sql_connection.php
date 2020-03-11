<?php
    try {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=derbyRowingClub', 'homestead', 'secret');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //Setting a persistent connection to remove overhead as the details above will not change
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        //Setting default fetch mode to get associative arrays
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e){
        echo "Error: " . $e -> getMessage() . "";
        die();
    }
