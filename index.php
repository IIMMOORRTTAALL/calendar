<?php
session_start();
include '../config.php';
$db = new PDO("mysql:host=localhost;dbname={$dbprefix}kalenderdb;charset=utf8", $username, $password);
 
if (isset($_POST['insert'])) {
  // 1) Skapa en SQL-fråga med platshållare (:xxx)
  // för värden från användaren
  $sql = "INSERT INTO data (StartTime, EndTime, event, Day, Colorpicker) VALUES (:StartTime, :EndTime, :event , :Day, :Colorpicker)";
 
  // 2) Skapa ett 'prepared statement'
  $ps = $db->prepare($sql);
 
  // 3) Knyt värden till platshållarna
  $ps->bindValue(':StartTime', $_POST['StartTime']);
  $ps->bindValue(':EndTime', $_POST['EndTime']);
  $ps->bindValue(':event', $_POST['event']);
  $ps->bindValue(':Day', $_POST['Day']);
  $ps->bindValue(':Colorpicker', $_POST['Colorpicker']);
 
  // 4) Kör frågan och testa om den kunde köras
  $ok = $ps->execute();
 
  if (!$ok) {
    $message = "Fel vid INSERT.";
  }
  header("Location: .");
}
 
if (isset($_POST['update'])) {
  $sql = "UPDATE person SET age=age+1 WHERE id=:id";
  $ps = $db->prepare($sql);
  $ps->bindValue(':id', $_POST['id']);
  $ok = $ps->execute();
 
  if (!$ok) {
    $message = "Fel vid UPDATE.";
  }
}
 
if (isset($_POST['delete'])) {
  $sql = "DELETE FROM person WHERE id=:id";
  $ps = $db->prepare($sql);
  $ps->bindValue(':id', $_POST['id']);
  $ok = $ps->execute();
 
  if (!$ok) {
    $message = "Fel vid DELETE.";
  }
}
 
 
?>
 
<!DOCTYPE html>
<html lang="en">
 
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="script.js" defer></script>
  <link rel="stylesheet" href="style.css">
  
 
<body>
 
 <div class = sidebar>
  <form method='post'>
    <p>Start tid:<input type="time" name='StartTime'></p>
    <p>Slut tid:<input type="time" name='EndTime'></p>
    <p>Event:<input name='event'></p>
    <p>Days:<select name='Days'>  
    <option>Måndag </option>
    <option>Tisdag</option>
    <option>Onsdag</option>
    <option>Torsdag</option>
    <option>Fredag</option>
    <option>Lördag</option>
    <option>Söndag</option>
</select></p>
    <p>Färg: <input type="color" name="Colorpicker"></p>
    <p><button name='insert'>Infoga Information.</button></p>
 
    <p>ID:<input name='id'></p>
    <p>
      <button name='update'> Update</button>
      <button name='delete'> Tabort</button>
    </p>
  </form>
 </div>
  <?php
  if (isset($message)) {
    echo "<p><b>$message</b></p>";
  }
  ?>
 
  <table>
    <tr>
      <th>Tid</th>
      <th>Monday</th>
      <th>Tuesday</th>
      <th>Wednesday</th>
      <th>Thursday</th>
      <th>Friday</th>
      <th>Saturday</th>
      <th>Sunday</th>
    </tr>
 
    <?php
    for ($i = 0; $i < 24; $i++) {
      echo "<tr>";
      echo "<th>", str_pad($i, 2, "0", STR_PAD_LEFT), ":00</th>";
 
      for ($j = 0; $j < 7; $j++) {
        echo "<td id='cell-$i-$j'></td>";
      }
 
      echo "</tr>";
    }
    ?>
  </table>
 
  <script>
    let cell;
  </script>
 
  <?php
  $days = ["Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag", "Söndag"];
 
  $sql = "SELECT * FROM data";
  $ps = $db->prepare($sql);
  $ps->execute();
 
  // Hämta rad-för-rad så länge det finns
  // någon rad att hämta
  while ($row = $ps->fetch()) {
    $id = $row['id'];
    $StartTime = $row['StartTime'];
    $EndTime = $row['EndTime'];
    $Event = $row['event'];
    $dag = $row['Day'];
    $Colorpicker = $row['Colorpicker'];
 
    $dayIndex = array_search($dag, $days);
    $startHour = intval(explode(":", $StartTime)[0]);
    $endHour = intval(explode(":", $EndTime)[0]);
 
    echo "<p>Start tid: $StartTime slut tid: $EndTime Event $Event och dag: $dag</p>";
 
    for ($i = $startHour; $i <= $endHour; $i++) {
      echo "<script>";
      echo "cell = document.getElementById('cell-$i-$dayIndex');";
 
      $style= "background-color: $Colorpicker; border-color: black; border-right: solid; border-left: solid;";
      
      if($i == $startHour) {
         echo "cell.innerHTML += '$Event' + '<br>';";
         $style .= "border-top: solid;";
      }
    
       if ($i == $endHour) { 
        $style .= "border-bottom: solid;";
      }
      echo "cell.style = '$style';";
      echo "cell.onclick = ()=>{alert('Start tid: $StartTime slut tid: $EndTime Event $Event och dag: $dag')};";
      echo "</script>";
    }
  }
 
  ?>
</body>
 
</html>