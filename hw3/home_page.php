<!DOCTYPE html>
<html>
  <head>
    <title>CPSC 431 HW-2</title>
  </head>
  <body>
    <h1 style="text-align:center">Cal State Fullerton Basketball Statistics</h1>

    <?php
      require_once('Address.php');
      require_once('PlayerStatistic.php');

      // Connect to database
//////// TO-DO:  Begin Student Region ///////////

      // Added to display extra error logging (VERY USEFUL)
      // ini_set('display_errors', 1);
      // ini_set('display_startup_errors', 1);
      // error_reporting(E_ALL);

      // $db = new mysqli('hostname', 'username', 'password', 'dbname')
      $db = new mysqli('localhost', 'coach', 'coachPassword123', 'CSUF_Basketball');
//////// END-TO-DO:  End Student Region ///////////


      // if connection was successful
//////// TO-DO:  Begin Student Region ///////////
      if (mysqli_connect_errno()) {
        echo '<p>Error: Could not connect to database.<br/>
        Please try again later.</p>';
        exit;
      }
//////// END-TO-DO:  End Student Region ///////////


        // Build query to retrieve player's name, address, and averaged statistics from the joined Team Roster and Statistics tables        
//////// TO-DO:  Begin Student Region ///////////
      
      // NOTE: No semicolon ';' inside the php query
      //       Always include a '?' in the query for the bind_param
      $query = "SELECT tr.ID, tr.Name_Last, tr.Name_First, tr.Street, tr.City, tr.State, tr.Country, tr.ZipCode,
                       AVG(s.PlayingTimeMin) AS AvgMin, AVG(s.PlayingTimeSec) AS AvgSec, 
                       AVG(s.Points) AS AvgPoints, AVG(s.Assists) AS AvgAssists, AVG(s.Rebounds) AS AvgRebounds,
                       COUNT(s.ID) AS GamesPlayed
                FROM TeamRoster AS tr
                LEFT JOIN Statistics AS s ON tr.ID = s.Player
                WHERE tr.Name_Last LIKE ?
                GROUP BY tr.ID, tr.Name_Last, tr.Name_First
                ORDER BY tr.Name_Last ASC, tr.Name_First ASC";

      // Note: the 'COUNT(s.ID) AS GamesPlayed' should be a part of the SELECT clause
      //       LEFT JOIN is extremely imp with the stats to the left, which won't display players without any stats or games played  
      //       'WHERE tr.Name_Last LIKE ?' allows us to filter by the last name, also very imp for the '?'
      //       'ASC' in ORDER BY means ascending

//////// END-TO-DO:  End Student Region ///////////


        // Prepare, execute, store results, and bind results to local variables
//////// TO-DO:  Begin Student Region ///////////
        // Prepare statement
        $stmt = $db->prepare($query);
        
        // Table does not display without this line
        $search = "%" . $searchTerm . "%";
        // NOTE: Num of elements in bind_param must = num of '?' in query
        $stmt->bind_param('s', $search);

        //Execute statement
        $stmt->execute();
        $stmt->store_result();    // store_result() requires to be paired with free_result()

        // Num of columns in SELECT as to be = Num of vars in bind_result
        //    14 columns = 14 variables
        $stmt->bind_result($playerID, $lastName, $firstName, $street, $city, $state, $country, $zipCode, 
                           $playingTimeMin, $playingTimeSec,
                           $avgPoints, $avgAssists, $avgRebounds,
                           $gamesPlayed);
//////// END-TO-DO:  End Student Region ///////////
    ?>

    <table style="width: 100%; border:0px solid black; border-collapse:collapse;">
      <tr>
        <th style="width: 40%;">Name and Address</th>
        <th style="width: 60%;">Statistics</th>
      </tr>
      <tr>
        <td style="vertical-align:top; border:1px solid black;">
          <!-- FORM to enter Name and Address -->
          <form action="processAddressUpdate.php" method="post">
            <table style="margin: 0px auto; border: 0px; border-collapse:separate;">
              <tr>
                <td style="text-align: right; background: lightblue;">First Name</td>
                <td><input type="text" name="firstName" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Last Name</td>
               <td><input type="text" name="lastName" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Street</td>
               <td><input type="text" name="street" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">City</td>
                <td><input type="text" name="city" value="" size="35" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">State</td>
                <td><input type="text" name="state" value="" size="35" maxlength="100"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Country</td>
                <td><input type="text" name="country" value="" size="20" maxlength="250"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Zip</td>
                <td><input type="text" name="zipCode" value="" size="10" maxlength="10"/></td>
              </tr>

              <tr>
               <td colspan="2" style="text-align: center;"><input type="submit" value="Add Name and Address" /></td>
              </tr>
            </table>
          </form>
        </td>

        <td style="vertical-align:top; border:1px solid black;">
          <!-- FORM to enter game statistics for a particular player -->
          <form action="processStatisticUpdate.php" method="post">
            <table style="margin: 0px auto; border: 0px; border-collapse:separate;">
              <tr>
                <td style="text-align: right; background: lightblue;">Name (Last, First)</td>
<!--            <td><input type="text" name="name" value="" size="50" maxlength="500"/></td>  -->
                <td><select name="name_ID" required>
                  <option value="" selected disabled hidden>Choose player's name here</option>
                  <?php
                    // for each row of data returned,
                    //   construct an Address object providing first and last name
                    //   emit an option for the pull down list such that
                    //     the displayed name is retrieved from the Address object
                    //     the value submitted is the unique ID for that player
                    // for example:
                    //     <option value="101">Duck, Daisy</option>
//////// TO-DO:  Begin Student Region ///////////
                      while ($stmt->fetch()) {
                        $fullName = $lastName . ", " . $firstName;
                        $newAddress = new Address($fullName);
                        echo "<option value=\"" . $playerID . "\">" . $newAddress->name() . "</option>";
                      }

//////// END-TO-DO:  End Student Region ///////////
                  ?>
                </select></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Playing Time (min:sec)</td>
               <td><input type="text" name="time" value="" size="5" maxlength="5"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Points Scored</td>
               <td><input type="text" name="points" value="" size="3" maxlength="3"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Assists</td>
                <td><input type="text" name="assists" value="" size="2" maxlength="2"/></td>
              </tr>

              <tr>
                <td style="text-align: right; background: lightblue;">Rebounds</td>
                <td><input type="text" name="rebounds" value="" size="2" maxlength="2"/></td>
              </tr>

              <tr>
               <td colspan="2" style="text-align: center;"><input type="submit" value="Add Statistic" /></td>
              </tr>
            </table>
          </form>
        </td>
      </tr>
    </table>


    <h2 style="text-align:center">Player Statistics</h2>

    <?php
      // emit the number of rows (records) in the table
//////// TO-DO:  Begin Student Region ///////////

      // NOTE: $stmt->num_rows can only be called after using $stmt->store_result()
      $numOfRows = $stmt->num_rows;

      echo "<p>Number of Records: " . $numOfRows . "</p>";
//////// END-TO-DO:  End Student Region ///////////
    ?>

    <table style="border:1px solid black; border-collapse:collapse;">
      <tr>
        <th colspan="1" style="vertical-align:top; border:1px solid black; background: lightgreen;"></th>
        <th colspan="2" style="vertical-align:top; border:1px solid black; background: lightgreen;">Player</th>
        <th colspan="1" style="vertical-align:top; border:1px solid black; background: lightgreen;"></th>
        <th colspan="4" style="vertical-align:top; border:1px solid black; background: lightgreen;">Statistic Averages</th>
      </tr>
      <tr>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;"></th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Name</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Address</th>

        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Games Played</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Time on Court</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Points Scored</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Number of Assists</th>
        <th style="vertical-align:top; border:1px solid black; background: lightgreen;">Number of Rebounds</th>
      </tr>
      <?php
//////// TO-DO:  Begin Student Region ///////////
//////// END-TO-DO:  End Student Region ///////////

        // for each row (record) of data retrieved from the database emit the html to populate a row in the table
        // for example:
        //  <tr>
        //    <td  style="vertical-align:top; border:1px solid black;">1</td>
        //    <td  style="vertical-align:top; border:1px solid black;">Dog, Pluto</td>
        //    <td  style="vertical-align:top; border:1px solid black;">1313 S. Harbor Blvd.<br/>Anaheim, CA 92808-3232<br/>USA</td>
        //    <td  style="vertical-align:top; border:1px solid black;">1</td>
        //    <td  style="vertical-align:top; border:1px solid black;">10:0</td>
        //    <td  style="vertical-align:top; border:1px solid black;">18</td>
        //    <td  style="vertical-align:top; border:1px solid black;">2</td>
        //    <td  style="vertical-align:top; border:1px solid black;">4</td>
        //  </tr>
        // or if there exists no statistical data for the player
        //  <tr>
        //    <td  style="vertical-align:top; border:1px solid black;">2</td>
        //    <td  style="vertical-align:top; border:1px solid black;">Duck, Daisy</td>
        //    <td  style="vertical-align:top; border:1px solid black;">1180 Seven Seas Dr.<br/>Lake Buena Vista, FL 32830<br/>USA</td>
        //    <td  style="vertical-align:top; border:1px solid black;">0</td>
        //    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        //    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        //    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        //    <td  style="border:1px solid black; border-collapse:collapse; background: #e6e6e6;"></td>
        //  </tr>
        //
//////// TO-DO:  Begin Student Region ///////////

      // Hey prof, I don't understnad why there are 3 different TO-DO sections here, so I split everything up into 3 parts

      // VERY IMP
      //   I am using the while($stmt->fetch()) 2 times, but the fetched data is thrown away
      //   The following line 'reels' back information we fetched previosly
      $stmt->data_seek(0);

      // Keep fetching results
      while ($stmt->fetch()) {
        
//////// END-TO-DO:  End Student Region ///////////


          // construct Address and PlayerStatistic objects supplying as constructor parameters the retrieved database columns
//////// TO-DO:  Begin Student Region ///////////

        // construct Address and PlayerStatistic objects supplying as constructor parameters the retrieved database columns
        $fullName = $lastName . ", " . $firstName;

        $address = new Address($fullName, (string)$street, (string)$city, (string)$state, (string)$country, (string)$zipCode);

        try {
          //                                Pass name as array,      and game time as array
          $time = $playingTimeMin . ":" . $playingTimeSec;
          $statistic = new PlayerStatistic($fullName, $time, 
                                          (float)$avgPoints, (float)$avgAssists, (float)$avgRebounds); 
        } catch (Exception $e) {
          // Construction of PlayerStatistic failed
          $statistic = null;
        } 

//////// END-TO-DO:  End Student Region ///////////


          // Emit table row data using appropriate getters from the Address and PlayerStatistic objects
//////// TO-DO:  Begin Student Region ///////////
        echo "<tr>";
        
        // Emit Player info
        echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $playerID . "</td>";

          // NOTE: Use '->' to access functions in PHP
        echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $address->name() . "</td>"; 
        echo "<td  style=\"vertical-align:top; border:1px solid black;\">" .  $address->street() . "<br/>" . 
              $address->city() . ", " . $address->state() . " " . $address->zip() . "<br/>" . $address->country() . "</td>";

        if ($gamesPlayed == 0) {
          // Player has not played any games
          echo "<td  style=\"vertical-align:top; border:1px solid black;\">0</td>";
          echo "<td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>";
          echo "<td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>";
          echo "<td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>";
          echo "<td  style=\"border:1px solid black; border-collapse:collapse; background: #e6e6e6;\"></td>";
        } else {
          // Player has played games, display that information
          echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $gamesPlayed . "</td>";
          echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $statistic->playingTime() . "</td>";
          echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $statistic->pointsScored() . "</td>";
          echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $statistic->assists() . "</td>";
          echo "<td  style=\"vertical-align:top; border:1px solid black;\">" . $statistic->rebounds() . "</td>";
        }
        echo "</tr>";

      }

      $stmt->free_result();
      $stmt->close();
      $db->close();
//////// END-TO-DO:  End Student Region ///////////
      ?>
    </table>

  </body>
</html>
