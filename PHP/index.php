<?php
  require_once 'FileSupervisor.php';
  $files = file_get_contents('files.json');
  $files = json_decode($files);
  print_r($files);
  $supervisor = new FileSupervisor('filesStatus.csv',$files);
  $supervisor -> runCheck();
  $supervisor -> resultToFile();
?>
