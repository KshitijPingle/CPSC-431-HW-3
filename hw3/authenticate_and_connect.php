<?php
// Call this file at the top of every single html page

require_once('/protected/Adaptation.php');

function authenticate_and_connect() {
    static $my_db_connection = null;

    if (isset($my_db_connection)) {
        // The user has already logged in and connected to the database, don't do anything else
        return;
    }

    
}

?>