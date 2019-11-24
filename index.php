<?php
  
  include('db_connect.php');

  $error = '';
  $name = '';
  $day_list = [];
  $week_list = [];

  if(isset($_POST['submit'])){

    if(empty($_POST['name'])) {
      $error = 'A beviteli mező nem lehet üres!';
    } else {
      $name = $_POST['name'];
      if(!preg_match('/^[a-zA-ZaáeéiíoóöőuúüűAÁEÉIÍOÓÖŐUÚÜŰ\s]+$/', $name)){
        $error = 'Érvénytelen karakter!';
      }
    }

    if($error == '') {
      $current_date = time();
      $name = mysqli_real_escape_string($conn, $_POST['name']);

      $sql = "INSERT INTO names(name, recorded_at) VALUES('$name','$current_date')";

      if(mysqli_query($conn, $sql)){
        $before_one_day = $current_date - (24 * 60 * 60);
        $before_one_week = $current_date - (7 * 24 * 60 * 60);

        $sql = "SELECT name, recorded_at, COUNT(name) AS value_occurence FROM names 
        WHERE recorded_at >= $before_one_day GROUP BY name ORDER BY value_occurence DESC LIMIT 5";
        $result = mysqli_query($conn, $sql);
        $day_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $sql = "SELECT name, recorded_at, COUNT(name) AS value_occurence FROM names 
        WHERE recorded_at >= $before_one_week GROUP BY name ORDER BY value_occurence DESC LIMIT 5";
        $result = mysqli_query($conn, $sql);
        $week_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

        mysqli_free_result($result);
        mysqli_close($conn);
      } else {
          echo 'query error: ' + mysqli_error($conn);
      }
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Most popular names</title>
  </head>
  <body>

    <form action="index.php" method="POST">
      <label>Név</label>
      <input type="text" name="name">
      <input type="submit" name="submit" value="Beküldés">
    </form>

    <p style="color: red"><?php echo $error; ?></p>

    <?php if (sizeof($day_list) > 0) { ?>
      <div class="day_list">
        <h2>Leggyakrabban keresett nevek az elmúlt egy napban:</h2>

        <table border="1">
          <thead>
            <tr>
              <th>Név</th>
              <th>Keresések</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($day_list as $name) { ?>
            <tr>
              <td><?php echo htmlspecialchars($name['name']); ?></td>
              <td><?php echo htmlspecialchars($name['value_occurence']); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>

    <?php if (sizeof($week_list) > 0) { ?>
      <div class="week_list">
        <h2>Leggyakrabban keresett nevek az elmúlt egy hétben:</h2>

        <table border="1">
          <thead>
            <tr>
              <th>Név</th>
              <th>Keresések</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($week_list as $name) { ?>
            <tr>
              <td><?php echo htmlspecialchars($name['name']); ?></td>
              <td><?php echo htmlspecialchars($name['value_occurence']); ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    <?php } ?>

  </body>
</html>