<?php
// File to delete a statistic

require_once('config.php');
require_once('authenticate_and_connect.php');
require_once('PlayerStatistic.php');
require_once('Address.php');

// If Coach, block them (per your rules: Coaches can only Read/Update stats)
if ($GLOBALS['role'] == 'coach') {
    die("Access Denied: Coaches cannot delete statistics.");
}

$playerID = 0;

if (isset($_POST['player_id'])) {
    $playerID = (int)$_POST['player_id'];
}


if ($playerID > 0) {
    try {
        // If the user is a Player, they can only delete their OWN stats
        //      So, check with a query
        if ($GLOBALS['role'] === 'player') {
            $check_query = "SELECT Name_Last, Name_First FROM TeamRoster WHERE ID = ?";
            $check_owner = $my_db_connection->prepare($check_query);
            $check_owner->bind_param("i", $playerID);
            $check_owner->execute();
            $check_owner->store_result();

            // Check if the record actually exists
            if ($check_owner->num_rows === 0) {
                die("Error: The statistic record you are trying to delete does not exist.");
            }

            // Selected 2 strings
            $check_owner->bind_result($player_last_name, $player_first_name);

            $check_owner->fetch();

            $full_name = $_SERVER['PHP_AUTH_USER'];

            // Get the first and last names of the logged in user
            $first_name = '';
            $last_name = '';

            if (str_contains($full_name, ',')) {
                // Username is in format 'Last_Name, First_Name'

                // Let Address class do the error handling for the name
                $new_addr = new Address($full_name);

                $first_name = '';

                $value = explode(',', $new_addr->name());   // convert string to array
                if (count($value) >= 2) {
                    // If we have 2 values, then the second value is the first name (Ex. Duck, Donald)
                    $first_name = trim($value[1]);
                }
                $last_name = trim($value[0]);
            } else {
                // Username is in format 'First_Name Last_Name'

                $value = explode(' ', $full_name);
                if (count($value) >= 2) {
                    // Assume the first word is the first name
                    $first_name = trim($value[0]);
                    // Assume everything else is the last name
                    unset($value[0]); 
                    $last_name = trim(implode(' ', $value));
                } else {
                    // Only one name entered
                    $last_name = trim($value[0]);
                }
            }

            $check_owner->close();

            // If the names don't match
            if (strcasecmp($last_name, trim($player_last_name)) || strcasecmp($first_name, trim($player_first_name))) {
                die("Access Denied: Players can delete only their own stats.");
            }
        } 

        // Delete query
        $query = "DELETE FROM Statistics WHERE Player = ?";
        $stmt = $my_db_connection->prepare($query);
        $stmt->bind_param('i', $playerID);
        $stmt->execute();
        $stmt->close();
        

        header("Location: home_page.php?msg=StatDeleted");
        exit;

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: home_page.php");
    exit;
}
?>