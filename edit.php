<?php
require_once "pdo.php";
session_start();

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if ( !isset($_SESSION['name']) ) {
  die('Not logged in');
}



if(isset($_POST['cancel'])){
  header("Location: index.php");
  return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

  if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1|| strlen($_POST['summary']) < 1) {
      $_SESSION['error'] = 'Missing data';
      header("Location: edit.php");
      return;
  }

  else if(strpos($_POST['email'], '@') == false){
    $_SESSION['error'] = 'Email must contain @';
    header("Location: edit.php?profile_id=". $_GET['profile_id']);
    return;
  }


  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      $_SESSION['error'] = 'All fields are required';
      header("Location: edit.php?profile_id=". $_GET['profile_id']);
      return;
    }

    if ( ! is_numeric($year) ) {
      $_SESSION['error'] = 'Position year must be numeric';
      header("Location: edit.php?profile_id=". $_GET['profile_id']);
      return;
    }
  }

  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['name'.$i]) ) continue;

    $year = $_POST['edu_year'.$i];
    $sch = $_POST['name'.$i];

    if ( strlen($year) == 0 || strlen($sch) == 0 ) {
      $_SESSION['error'] = 'All fields are required';
      header("Location: edit.php?profile_id=". $_GET['profile_id']);
      return;
    }

    if ( ! is_numeric($year) ) {
      $_SESSION['error'] = 'Education year must be numeric';
      header("Location: edit.php?profile_id=". $_GET['profile_id']);
      return;
    }
  }

    $sql = "UPDATE profile SET first_name = :first_name,
            last_name = :last_name, email = :email,
            headline = :headline, summary = :summary
            WHERE profile_id = :profile_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':user_id' => $_SESSION['user_id'],
        ':profile_id' => $_POST['profile_id']));

        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        $rank = 1;
          for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;

            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            $stmt = $pdo->prepare('INSERT INTO Position
              (profile_id, rank, year, description)
              VALUES ( :pid, :rank, :year, :desc)');

            $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank++,
            ':year' => $year,
            ':desc' => $desc)
            );

          }

          $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
          $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

          $rank = 1;
            for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['edu_year'.$i]) ) continue;
              if ( ! isset($_POST['name'.$i]) ) continue;

              $sc = $_POST['name'.$i];

              $sc = $_POST['name'.$i];
              $stm = $pdo->prepare('Delete  From  Institution where name = \''.$sc.'\'');
              $stm->execute();

                $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES(:nam)');
                $stmt->execute(array(':nam' => $sc));
                $in_id = $pdo->lastInsertId();


              $stmt = $pdo->prepare('INSERT INTO education
                (profile_id, institution_id, rank, year)
                VALUES ( :pid, :iid, :rank, :year)');

              $stmt->execute(array(
              ':pid' => $_REQUEST['profile_id'],
              ':iid' => $in_id,
              ':rank' => $rank++,
              ':year' => $year)
              );

              $rank++;
            }

    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;


    return;
}

// Guardian: Make sure that profile_id is present
if ( !isset($_GET['profile_id']) && !isset($_POST['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

if ( ! isset($_SESSION['user_id']) ) {
  $_SESSION['error'] = "NOt LOGGED IN!";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}


if($_SESSION['user_id'] != $row['user_id']){
    $_SESSION['error'] = "User id does not match!";
    header('Location: index.php');
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$profile_id = $row['profile_id'];

$stmp = $pdo->query('SELECT year, description FROM position where profile_id ='. $_GET['profile_id'] . '  order by rank asc');
$stm = $pdo->query('SELECT year, name FROM education inner join institution on education.institution_id = institution.institution_id where profile_id ='. $_GET['profile_id'] . '  order by rank asc');
$counter = 0;
$counteredu = 0;
?>
<head>
<title>Siniuc Robert-Valentin</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

</head>
<p>Edit profile</p>

<form method="post">
<p>First Name:
<input type="text" name="first_name" value="<?php echo(htmlentities($row['first_name'])) ?>"></p>
<p>Last Name:
<input type="text" name="last_name" value="<?php echo(htmlentities($row['last_name'])) ?>"></p>
<p>Email:
<input type="text" name="email" value="<?php echo(htmlentities($row['email'])) ?>"></p>
<p>Headline:<br>
<input type="text" name="headline" value="<?php echo(htmlentities($row['headline'])) ?>"></p>
<p>Summary:<br>
<textarea name="summary" rows="8" cols="80" > <?php echo(htmlentities($row['summary'])) ?> </textarea></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<p>Education: <input type="submit" id="addPos_edu" value="+">
  <div id="education_fields">

    <?php

      while ( $row_3 = $stm->fetch(PDO::FETCH_ASSOC) ) {
        echo ('<div id="education'.++$counteredu.'">');
        echo ('<p>Year: <input type="text" name="edu_year'.$counteredu.'" value="'.$row_3['year'].'" />');
        echo ('<input type="button" value="-" onclick="$(\'#education'.$counteredu.'\').remove();return false;">');
        echo ('</p>');
        echo ('<p>Name: <input type="text" name="name'.$counteredu.'"class="school" value="'.$row_3['name'].'" />');
        echo ('</p>');
        echo ('</div>');
      }
    ?>

  </div>
</p>
</div>
<p>Position: <input type="submit" id="addPos" value="+">
  <div id="position_fields">
    <?php

      while ( $row_2 = $stmp->fetch(PDO::FETCH_ASSOC) ) {
        echo ('<div id="position'.++$counter.'">');
        echo ('<p>Year: <input type="text" name="year'.$counter.'" value="'.$row_2['year'].'" />');
        echo ('<input type="button" value="-" onclick="$(\'#position'.$counter.'\').remove();return false;">');
        echo ('</p>');
        echo ('<textarea name="desc'.$counter.'" rows="8" cols="80">');
        echo ($row_2['description']);
        echo ('');
        echo ('</textarea>');
        echo ('</div>');
      }
    ?>

  </div></p>
<p><input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel"></p>
</form>

<script>
countPos = <?php echo $counter ?>;
countPosEdu = <?php echo $counteredu ?>;
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
            '<div id="position'+countPos +'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
            onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addPos_edu').click(function(event){

        event.preventDefault();
        if ( countPosEdu >= 9 ) {
            alert("Maximum of nine Education entries exceeded");
            return;
        }
        countPosEdu++;
        window.console && console.log("Adding education "+countPosEdu);
        $('#education_fields').append(
            '<div id="education'+countPosEdu +'"> \
            <p>Year: <input type="text" name="edu_year'+countPosEdu+'" value="" /> \
            <input type="button" value="-" \
            onclick="$(\'#education'+countPosEdu+'\').remove();return false;"></p> \
            <p>Name: <input type="text" name="name'+countPosEdu+'" class="school" value="" /></p>\
            </div>');
    });

    $('.school').autocomplete({
        source: "school.php"
    });
});
</script>
</div>
</body>
</html>
