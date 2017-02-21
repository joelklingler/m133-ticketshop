<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <title>Ticketshop</title>
  <!-- Import my style -->
  <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <!--Import Google Icon Font-->
  <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Import materialize.css-->
  <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
  <!--Import jQuery and materialize.js-->
  <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script type="text/javascript" src="js/materialize.min.js"></script>
  <?php
  include 'pages/loader.php';
  include 'pages/loginForm.php';
  $seite = 'pages/home.php';
  if(@$_GET['seite'])
  {
    $seite = 'pages/'.@$_GET['seite'];
  }
  include 'pages/nav.php';
  ?>
  <div id="content" class="container">
  <?php
  include $seite;
  ?>
  </div>
  <?php
  include 'pages/footer.php';
  ?>
  <!--  Scripts-->
  <script src="js/script.js"></script>
  <script src="js/init.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
</body>
</html>