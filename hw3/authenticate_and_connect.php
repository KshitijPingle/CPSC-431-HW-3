<?php
// Call this file at the top of every single html page

require_once('/protected/Adaptation.php');
require_once('Address.php');
require_once('PlayerStatistic.php');

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

    // Log into database using visitor information
    // $db = new mysqli('hostname', 'username', 'password', 'dbname')
    $my_db_connection = new mysqli(DATA_BASE_HOST, 'Visitor', ACCOUNTS['Visitor'], DATA_BASE_NAME);

    // Check if user has not logged in (we won't have usernames in that case)
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        sendAuthenticationHeader();
    } else {
        // Get username and password
        $full_name = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Let Address class do the error handling for the name
        $new_addr = new Address($full_name);

        $first_name = '';

        $value = explode(',', $new_addr->name());   // convert string to array
        if (count($value) >= 2) {
            // If we have 2 values, then the second value is the first name (Ex. Duck, Donald)
            $first_name = trim($value[1]);
        }
        $last_name = trim($value[0]);

        $query = "SELECT a.PasswordHash 
                  FROM Accounts a
                  JOIN TeamRoster r ON a.UserID = r.ID
                  WHERE r.Name_Last = ? AND r.Name_First = ?";
        
        $stmt = $my_db_connection->prepare($query);
        $stmt->bind_param("ss", $last_name, $first_name);       // 2 's' for 2 strings and 2 '?'
        $stmt->execute();


    }


}

?>