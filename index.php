
<?php

require_once "pdo.php";

session_start();

if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}

?>

<html>
<head>
<title>Siniuc Robert-Valentin</title>

<link rel="stylesheet" href="index-style.css">
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;600&display=swap" rel="stylesheet">

</head><body>
<h1>Bob's Resume Registry</h1>
<a href="login.php">Please log in</a>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}

$stnr = $pdo->prepare('Select profile_id from profile');
$stnr->execute();


if(!isset($_SESSION['name'])){

  if($stnr->rowCount() != 0) {
    echo('<table border="1">'."\n");
    $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id, user_id FROM profile");
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $name = htmlentities($row['first_name']). ' ' .htmlentities($row['last_name']);

      echo "<tr><td>";
      echo('<a href="view.php?profile_id='.$row['profile_id'].'">'. $name .'</a> ');
      echo("</td><td>");
      echo(htmlentities($row['headline']));
      echo("</td></tr>\n");
      }
    }
}

else{

  if($stnr->rowCount() != 0){
  echo('<table border="1">'."\n");
  $stmt = $pdo->query("SELECT first_name, last_name, headline, profile_id, user_id FROM profile");
  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

      $name = htmlentities($row['first_name']). ' ' .htmlentities($row['last_name']);

      echo "<tr><td>";
      echo('<a href="view.php?profile_id='.$row['profile_id'].'">'. $name .'</a> ');
      echo("</td><td>");
      echo(htmlentities($row['headline']));

      if($row['user_id'] == $_SESSION['user_id']){
        echo("</td><td>");
        echo('<a href="edit.php?profile_id='.$row['profile_id'] .'">Edit</a>  / ');
        echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
        }
      }

      echo("</td></tr>\n");

  }
  echo("<form method="."POST".">");
  echo("\n <input type="."submit"." name="."logout"." value="."Logout".">");
  echo "<br>";
  echo("<a href="."add.php".">Add New Entry</a>");
}
?>


</body>
</html>
