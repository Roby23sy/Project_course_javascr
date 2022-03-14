<?php
session_start();
require_once "pdo.php";


if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

$failure = false;  // If we have no POST data



  if ( isset($_POST['email']) && isset($_POST['pass']) ){

    $check = hash('md5', $salt.$_POST['pass']);

        $stmt = $pdo->prepare('SELECT user_id, name FROM users

            WHERE email = :em AND password = :pw');

        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);


    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
          $_SESSION['error'] = "Email or Password missing";
          header("Location: login.php");
          error_log("Login fail ".$_POST['email']." $check");
          return;
        }

    else if (!str_contains($_POST['email'], '@')){
          $_SESSION['error'] = "Email must have an at-sign (@)";
          header("Location: login.php");
          error_log("Login fail ".$_POST['email']." $check");
          return;
        }

    else {
      $check = hash('md5', $salt.$_POST['pass']);
        if ( $row !== false ) {

          $_SESSION['name'] = $row['name'];
          $_SESSION['user_id'] = $row['user_id'];

          // Redirect the browser to index.php

         header("Location: index.php");

          return;
        }

        else {
          $_SESSION['error'] = "Wrong Password";
          header("Location: login.php");
          error_log("Login fail ".$_POST['email']." $check");
          return;
        }

      }
  }


// Fall through into the View
?>


<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Siniuc Robert-Valentin</title>

<link rel="stylesheet" href="login.css">
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
 <link  href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;600&display=swap" rel="stylesheet">

</head>
<body>
<div class="container">
<h1>Please Log In </h1>

<?php
    if ( isset($_SESSION['error']) ) {
      echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
      unset($_SESSION['error']);
    }
?>

<form method="POST" action="login.php">
<label for="nam">Email</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input class="button" type="submit" onclick="return doValidate();" value="Log In">
<input class="button" type="submit" name="cancel" value="Cancel">
</form>
<div class="secret">
 <p>email: umsi@umich.edu</p>
 <p>pass: php123</p>
</div>

<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('nam').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>

</div>
</body>
