<?php
require_once('config.php');

// Everything works now

// Added to display extra error logging (VERY USEFUL)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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

require('Address.php');

// Connect with database
$db = new mysqli('localhost', 'coach', 'coachPassword123', 'CSUF_Basketball');

if (mysqli_connect_errno()) {
  echo '<p>Error: Could not connect to database.<br/>
  Please try again later.</p>';
  exit;
}

// Always place INSERT stmts inside a try and catch
try {
  // Note: 7 columns, so 7 '?'
  //       Since we had TeamRoster.ID as auto-incremented, we don't insert it
  $query = "INSERT INTO TeamRoster (Name_First, Name_Last, Street, City, State, Country, ZipCode)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
  
  $stmt = $db->prepare($query);

  // 7 '?' in query, so 7 variables
  $stmt->bind_param('sssssss', $firstName, $lastName, $street, $city, $state, $country, $zip);

  $stmt->execute();
  // Do not store results for INSERT stmt
} catch (Exception $e) {
  echo '<p>Error: while inserting into the database.<br/>
  Please try again later.</p>';
  exit;
}

// Check if we successfully inserted
if ($stmt->affected_rows < 0) {
  echo"<p>Error: Insert statement made no changes to the database.<br/>
  Please try again later.</p>";
  exit;
}

$stmt->close();
$db->close();

// Lexically include and execute the home page file content so the home page is displayed after the update completes
require('home_page.php');

?>