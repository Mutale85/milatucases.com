<?php
    session_start();
    $PASS = "Javeria##2019";
    $USER = "MutaleMulenga";
    $dbname = "mila_MilatucasesDb";

	try {
        // Attempt to connect to the database
        $connect = new PDO("mysql:host=localhost;dbname=$dbname;", $USER, $PASS);
        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        include 'functions.php';
        ini_set("pcre.jit", "0");
        if ($connect) {
            //echo "Connected to the database successfully.";
        } else {
            echo "Failed to connect to the database.";
        }
    } catch(PDOException $e) {
        // Handle database connection errors
        echo "Connection failed: " . $e->getMessage();
    }

?>