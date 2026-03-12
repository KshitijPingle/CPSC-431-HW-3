<?php
require_once('config.php');

// Everything works now

// Added to display extra error logging (VERY USEFUL)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


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

$mins = substr($newStat->playingTime(), 0, $colonIndex);      // From start until colon
$secs = substr($newStat->playingTime(), $colonIndex + 1);     // From colon + 1 until the end

require('PlayerStatistic.php');

// Connect with database
$db = new mysqli('localhost', 'coach', 'coachPassword123', 'CSUF_Basketball');

if (mysqli_connect_errno()) {
  echo '<p>Error: Could not connect to database.<br/>
  Please try again later.</p>';
  exit;
}

try {
  // 6 columns, so 6 '?'
  $query = "INSERT INTO Statistics (Player, PlayingTimeMin, PlayingTimeSec, Points, Assists, Rebounds)
            VALUES (?, ?, ?, ?, ?, ?)";

  $stmt = $db->prepare($query);

  // 6 '?' in the query, so 6 variables
  // NOTE: 'i' = int, 'd' = float, 's' = string, 'b' = blob
  $stmt->bind_param('iiiiii', $id, $mins, $secs, $newStat->pointsScored(), $newStat->assists(), $newStat->rebounds());

  $stmt->execute();

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
