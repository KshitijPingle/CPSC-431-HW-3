<?php
// Logs out user in a round about way

// Send an unauthorized to clear browser cache
header('HTTP/1.1 401 Unauthorized');
header('WWW-Authenticate: Basic realm="Logged Out"');

echo '<h1>You have been logged out.</h1>';
echo '<p><a href="home_page.php">Click here to log back in.</a></p>';
exit;
?>