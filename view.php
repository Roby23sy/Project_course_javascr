<?php
require_once "pdo.php";
require_once "once.php";
session_start();

if(!isset($_GET['profile_id'])){

  $_SESSION['error'] = "No profile selected to view!";
  header("Location: index.php");
  return;
}

$stmt = $pdo->query('SELECT first_name, last_name, email, headline, summary FROM profile where profile_id ='. $_GET['profile_id']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmp = $pdo->query('SELECT year, description FROM position where profile_id ='. $_GET['profile_id'] . '  order by rank asc');
$stme = $pdo->query('SELECT year, name FROM education inner join institution on education.institution_id = institution.institution_id where profile_id ='. $_GET['profile_id'] . '  order by rank asc');
?>


<html>
<head>
<title>Siniuc Robert-Valentin</title>

<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

</head><body>
<h1>Profile information</h1>
<br>
<p>First Name: <?php echo(htmlentities($row['first_name'])); ?> </p>
<p>Last Name: <?php echo(htmlentities($row['last_name'])); ?> </p>
<p>Email: <?php echo(htmlentities($row['email'])); ?> </p>
<p>Headline: </p>
  <p> <?php echo(htmlentities($row['headline'])); ?> </p>
<p>Summary: </p>
  <p> <?php echo(htmlentities($row['summary'])); ?> </p>

<p>Education</p><ul>
  <?php
    while ( $row_3 = $stme->fetch(PDO::FETCH_ASSOC) ) {
      echo ('<li>'. $row_3['year'] .':  '. htmlentities($row_3['name']) .'</li>');
      }
  ?>
</ul>

<p>Position</p><ul>
  <?php
    while ( $row_2 = $stmp->fetch(PDO::FETCH_ASSOC) ) {
      echo ('<li>'. $row_2['year'] .':  '. htmlentities($row_2['description']) .'</li>');
    }
  ?>
</ul>
<a href="index.php">Done</a>

</body>
</html>
