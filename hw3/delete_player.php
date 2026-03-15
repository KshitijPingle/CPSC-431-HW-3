<?php
require_once('config.php');
require_once('authenticate_and_connect.php');

// Get the player id
if (!isset($_POST['player_id'])) {
    header("Location: home_page.php");
    exit;
}

$id = (int)$_POST['player_id'];

try {
    // Delete Player
    $query = "DELETE FROM TeamRoster WHERE ID = ?";
    $stmt = $my_db_connection->prepare($query);
    $stmt->bind_param('i', $id);
    
    $stmt->execute();

    $stmt->close();

} catch (Exception $e) {
    die("Error deleting player: " . $e->getMessage());
}

// Go back to home_page.php
header("Location: home_page.php?deleted=1");
exit;
?>