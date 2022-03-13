<?php
require_once "pdo.php";

session_start();
if ( !isset($_SESSION['name']) ) {
  die('Not logged in');
}


function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}


  if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1|| strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    }

    else if(strpos($_POST['email'], '@') == false){
      $_SESSION['error'] = 'Email must contain @';
      header("Location: add.php");
      return;
    }


    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;

      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];

      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
      }

      if ( ! is_numeric($year) ) {
        $_SESSION['error'] = 'Position year must be numeric';
        header("Location: add.php");
        return;
      }
    }


  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $edu_year = $_POST['edu_year'.$i];
    $edu_desc = $_POST['edu_school'.$i];

    if ( strlen($edu_year) == 0 || strlen($edu_desc) == 0 ) {
      $_SESSION['error'] = 'All fields are required';
      header("Location: add.php");
      return;
    }

    if ( ! is_numeric($edu_year) ) {
      $_SESSION['error'] = 'Education year must be numeric';
      header("Location: add.php");
      return;
    }
  }





            $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');

        $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
        );



        $rank = 1;
          $profile_id = $pdo->lastInsertId();


        for($i=1; $i<=9; $i++) {

          if ( ! isset($_POST['year'.$i]) ) continue;
          if ( ! isset($_POST['desc'.$i]) ) continue;

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

        $stmt->execute(array(
          ':pid' => $profile_id,
          ':rank' => $rank,
          ':year' => $_POST['year'.$i],
          ':desc' => $_POST['desc'.$i])
        );

        $rank++;
      }

      $rank = 1;

      for($i=1; $i<=9; $i++) {

        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;

        $sc = $_POST['edu_school'.$i];
        $stm = $pdo->prepare('Delete  From  Institution where name = \''.$sc.'\'');
        $stm->execute();

          $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES(:nam)');
          $stmt->execute(array(':nam' => $sc));
          $inst_id = $pdo->lastInsertId();



      $stmt = $pdo->prepare('INSERT INTO education (profile_id, institution_id, rank, year) VALUES ( :pid, :iid, :rank, :edu_year)');

      $stmt->execute(array(
        ':pid' => $profile_id,
        ':iid' => $inst_id,
        ':rank' => $rank,
        ':edu_year' => $_POST['edu_year'.$i])
      );

      $rank++;
    }


      $_SESSION['success'] = "Profile added";
       header("Location: index.php");
       return;
  }



// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}


?>

<html>
<head>
<title>Siniuc Robert-Valentin</title>

<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous"> -->

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>


</head>
<p>Adding Profile for <?php echo $_SESSION['name']; ?> </p>
<form method="post">
<p>First Name:
<input type="text" name="first_name"></p>
<p>Last Name:
<input type="text" name="last_name"></p>
<p>Email:
<input type="text" name="email"></p>
<p>Headline:
  <br>
<input type="text" name="headline" size="40"></p>
<p>Summary:
  <br>
<textarea name="summary" rows="8" cols="80"></textarea></p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</form>

<script>



countPos = 0;
countEdu = 0;

$(document).ready(function(){
    window.console && console.log('Document ready called');

    $('#addPos').click(function(event){

        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"><br>\
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });

});

</script>

</div>
</body>
</html>
