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
    static $my_db_connection = new mysqli(DATA_BASE_HOST, 'Visitor', DBPASSWORDS['Visitor'], DATA_BASE_NAME);


    if (mysqli_connect_errno()) {
        echo '<p>Error: Could not connect to database.<br/>
        Please try again later.</p>';
        exit;
      }

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

        // Query to get password Hash and User role
        $query = "SELECT a.PasswordHash, r.RoleName 
                  FROM Accounts a
                  JOIN Roles r ON a.RoleID = r.ID
                  JOIN TeamRoster tr ON a.UserID = tr.ID
                  WHERE tr.Name_Last = ? AND tr.Name_First = ?";
        
        $stmt = $my_db_connection->prepare($query);
        $stmt->bind_param("ss", $last_name, $first_name);       // 2 's' for 2 strings and 2 '?'
        $stmt->execute();

        $stmt->store_result();      // Note: store_result() requires to be paired with free_result()

        // Num of columns in SELECT as to be = Num of vars in bind_result
        //    2 columns = 2 variables
        $stmt->bind_result($passHash, $roleName);

        $stmt->free_result();

        if (password_verify($password, $passHash)) {
            // User is authorized, now connect to database with the correct role
            $my_db_connection->close();

            // Log into database using the correct role information
            // $db = new mysqli('hostname', 'username', 'password', 'dbname')
            $my_db_connection = new mysqli(DATA_BASE_HOST, $roleName, DBPASSWORDS[$roleName], DATA_BASE_NAME);

            // No need to return the db connection since it is static
        } else {
            header('WWW-Authenticate: Basic realm="Team Portal"');
            exit('Invalid Password.');
        }
    }


}

?>