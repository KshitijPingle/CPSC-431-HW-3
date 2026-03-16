<?php
require_once('config.php');
require_once('authenticate_and_connect.php');


$id         = trim($_POST['name_ID']);
$time       = trim( preg_replace("/\t|\R/",' ', $_POST['time']));
$points     = (int) $_POST['points'];
$assists    = (int) $_POST['assists'];
$rebounds   = (int) $_POST['rebounds'];

$colonIndex = strpos($time, ':');
$timeMin = (int)substr($time, 0, $colonIndex);    // From start until colon
$timeSec = (int)substr($time, $colonIndex + 1);   // From colon + 1 until the end

$time_after_checks = $timeMin . ':' . $timeSec;

// Make a statistic object and delegate error handling to it
$newStat = new PlayerStatistic('', $time_after_checks, $points, $assists, $rebounds);

require_once('PlayerStatistic.php');

// Note: No requirement in docs to stop players from changing stats of another player

$inserting = FALSE;

if (mysqli_connect_errno()) {
  echo '<p>Error: Could not connect to database.<br/>
  Please try again later.</p>';
  exit;
}

try {

  // Check if the stat already exists to know if we are updating or adding a stat
  $check_query = "SELECT ID FROM Statistics WHERE Player = ?";
  $check_stmt = $my_db_connection->prepare($check_query);
  $check_stmt->bind_param('i', $id);
  $check_stmt->execute();
  $check_stmt->store_result();

  // Convert to variables to avoid an error
  $pts = $newStat->pointsScored();
  $asst = $newStat->assists();
  $reb = $newStat->rebounds();

  if ($check_stmt->num_rows > 0) {
    // Update Stat

    $query = "UPDATE Statistics
             SET PlayingTimeMin = ?, PlayingTimeSec = ?, Points = ?, Assists = ?, Rebounds = ? 
             WHERE Player = ?";
    
    $stmt = $my_db_connection->prepare($query);

    // 6 '?', so 6 variables   (Note: ID has to be the last variable)
    $stmt->bind_param('iiiiii', $timeMin, $timeSec, $pts, $asst, $reb, $id);

  } else {
    // Add Stat
    $inserting = TRUE;

    if ($GLOBALS['role'] == 'coach') {
      // Coaches cannot add or delete player stats
      die("Access Denied: Coaches cannot add or delete stats");
    }

    // 6 columns, so 6 '?'
    $query = "INSERT INTO Statistics (Player, PlayingTimeMin, PlayingTimeSec, Points, Assists, Rebounds)
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $my_db_connection->prepare($query);

    // 6 '?' in the query, so 6 variables
    // NOTE: 'i' = int, 'd' = float, 's' = string, 'b' = blob
    $stmt->bind_param('iiiiii', $id, $timeMin, $timeSec, $pts, $asst, $reb);
  }

  $stmt->execute();

} catch (Exception $e) {
  echo '<p>Error: while inserting into the database.<br/>
  Please try again later.</p>';
  echo '<p>Caught exception: ';
  echo $e->getMessage();
  echo '</p>';
  exit;
}

// Check if we successfully inserted
//    Note: Do not check this if we are updating, since affected_rows will be zero
if (($inserting) && ($stmt->affected_rows <= 0)) {
  echo"<p>Error: Insert statement made no changes to the database.<br/>
  Please try again later.</p>";
  exit;
}

$check_stmt->close();
$stmt->close();
// Do not close connection to db

// Lexically include and execute the home page file content so the home page is displayed after the update completes
require('home_page.php');
?>
