<?php
// Call this file at the top of every single html page

require_once('/protected/Adaptation.php');

function sendAuthenticationHeader() {
    // Sends an authentication header to the browser
    //  This tells the browser to ask user for username and password
    //  Allows us to be able to use '$_SERVER['PHP_AUTH_USER]
    
    header('WWW-Authenticate: Basic realm="Team Portal"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Please enter your username and password';
    exit;
}

function authenticate_and_connect() {
    static $my_db_connection = null;

    if (isset($my_db_connection)) {
        // The user has already logged in and connected to the database, don't do anything else
        return;
    }


}

?>