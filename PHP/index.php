<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
</head>
<body>
  <a href="result.csv">result</a>
  <a href="filesStatus.csv">stats</a>
  <br>
</body>
</html>
<?php
  require_once 'src/FileSupervisor.php';
  // require_once 'src.old/FileSupervisor.php';
  $files = file_get_contents('files.json');
  $files = json_decode($files);
  print_r($files);
  $supervisor = new FileSupervisor('filesStatus.csv',$files);
  $supervisor -> runCheck();
  $supervisor -> resultToFile();
?>
