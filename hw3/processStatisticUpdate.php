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

// Make a statistic object and delegate error handling to it
$newStat = new PlayerStatistic('', $timeMin, $timeSec, $points, $assists, $rebounds);

// $mins = substr($newStat->playingTime(), 0, $colonIndex);      // From start until colon
// $secs = substr($newStat->playingTime(), $colonIndex + 1);     // From colon + 1 until the end

require_once('PlayerStatistic.php');

// Connect with database
// $db = new mysqli('localhost', 'coach', 'coachPassword123', 'CSUF_Basketball');

if (mysqli_connect_errno()) {
  echo '<p>Error: Could not connect to database.<br/>
  Please try again later.</p>';
  exit;
}

try {
  // 6 columns, so 6 '?'
  $query = "INSERT INTO Statistics (Player, PlayingTimeMin, PlayingTimeSec, Points, Assists, Rebounds)
            VALUES (?, ?, ?, ?, ?, ?)";

  $stmt = $my_db_connection->prepare($query);

  // Convert to variables to avoid an error
  $pts = $newStat->pointsScored();
  $stat = $newStat->assists();
  $reb = $newStat->rebounds();

  // 6 '?' in the query, so 6 variables
  // NOTE: 'i' = int, 'd' = float, 's' = string, 'b' = blob
  $stmt->bind_param('iiiiii', $id, $timeMin, $timeSec, $pts, $stat, $reb);

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
if ($stmt->affected_rows <= 0) {
  echo"<p>Error: Insert statement made no changes to the database.<br/>
  Please try again later.</p>";
  echo '<p>Caught exception: ';
  echo $e->getMessage();
  echo '</p>';
  exit;
}

$stmt->close();
// Do not close connection to db

// Lexically include and execute the home page file content so the home page is displayed after the update completes
require('home_page.php');
?>
