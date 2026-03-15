<?php
require_once('config.php');
require_once('authenticate_and_connect.php');


// Collect data from form
$firstName = trim($_POST['firstName']);
$lastName  = trim($_POST['lastName']);
$street    = trim($_POST['street']);
$city      = trim($_POST['city']);
$state     = trim($_POST['state']);
$country   = trim($_POST['country']);
$zip       = trim($_POST['zipCode']);

// Replace anything that isn't a number or hyphen
$zip = preg_replace('/[^\d-]/', '', $zip);

// Last name is required

$fullName = $lastName . ", " . $lastName;

// Ensure zip code also follows the correct pattern
// Note: PHP requires the '/' at start and end for regexp
//       The '^' and '$' at the start and end ensure the whole string matches, basically not allowing more chars
$zipRegex = '/^(?!0{5})(?!9{5})\d{5}(-(?!0{4})(?!9{4})\d{4})?$/'; 


if (!preg_match($zipRegex, $zip)) {
    
  exit("Error: Zip code does not follow the correct pattern");
}

require_once('Address.php');

$inserting = FALSE;

if (mysqli_connect_errno()) {
  echo '<p>Error: Could not connect to database.<br/>
  Please try again later.</p>';
  exit;
}

// Always place INSERT stmts inside a try and catch
try {

  // Check if the player already exists to know if we are updating or adding a player
  $check_query = "SELECT ID FROM TeamRoster WHERE Name_First = ? AND Name_Last = ?";
  $check_stmt = $my_db_connection->prepare($check_query);
  $check_stmt->bind_param('ss', $firstName, $lastName);
  $check_stmt->execute();
  $check_stmt->store_result();

  if ($check_stmt->num_rows > 0) {
    // Update Player

    // Query to update only the street and city (Players can do this)
    $query = "UPDATE TeamRoster SET Street = ?, City = ?, State = ?, Country = ?, ZipCode = ?
              WHERE Name_First = ? AND Name_Last = ?";
    $stmt = $my_db_connection->prepare($query);
    $stmt->bind_param('sssssss', $street, $city, $state, $country, $zip, $firstName, $lastName);  // 7 variables

  } else {
    // Add Player
    $inserting = TRUE;

    // Note: 7 columns, so 7 '?'
    //       Since we had TeamRoster.ID as auto-incremented, we don't insert it
    $query = "INSERT INTO TeamRoster (Name_First, Name_Last, Street, City, State, Country, ZipCode)
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $my_db_connection->prepare($query);

    // 7 '?' in query, so 7 variables
    $stmt->bind_param('sssssss', $firstName, $lastName, $street, $city, $state, $country, $zip);
  }

  $stmt->execute();
  // Do not store results for INSERT stmt

} catch (Exception $e) {
  echo '<p>Error: while inserting into the database.<br/>
  Please try again later.</p>';
  exit;
}

// Check if we successfully inserted, and only check if we are inserting
if (($inserting) && ($stmt->affected_rows <= 0)) {
  echo"<p>Error: Insert statement made no changes to the database.<br/>
  Please try again later.</p>";
  exit;
}

$stmt->close();
// Do not close connection to db

// Lexically include and execute the home page file content so the home page is displayed after the update completes
require('home_page.php');

?>