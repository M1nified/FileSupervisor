<?php
/**
*
*/
class FileSupervisor
{

  function __construct($statusFile,$filesRegExps)
  {
    // echo 'STATUS FILE IS '.$statusFile;
    $this -> statusFile = $statusFile;
    $this -> filesRegExps = $filesRegExps;
    $this -> checkResult = [];
    $this -> stats = [];
    $stats = [];
    if (($statsHandler = fopen ($this -> statusFile,"r")) !== FALSE) {
      // throw new Exception("Error while accessing stats file", 1);
      while(!feof($statsHandler)){
        $stats[] = fgetcsv($statsHandler);
      }
    }
    fclose($statsHandler);
    // echo "READ CSV";
    // print_r($stats);
    foreach ($stats as $row) {
      // echo $row;
      $this -> stats[$row[0]] = $row;
    }
    // echo "##### STATS BELOW: #####\n";
    // print_r($this -> stats);
  }
  function runCheck(){
    // echo "\n";
    foreach ($this -> filesRegExps as $fileRegExp) {
      // echo "CHECKING ".$fileRegExp;
      $matchedFiles = glob($fileRegExp);
      // print_r($matchedFiles);
      $this -> checkFilesByName($matchedFiles);
    }
    $statsHandler = fopen($this -> statusFile,"w+");
    foreach ($this -> stats as $key => $line){
      $record = explode(',',$line);
      array_unshift($record,$key);
      fputcsv($statsHandler,$record);
    }
    fclose($statsHandler);
  }
  private function checkFilesByName($filePaths){
    foreach ($filePaths as $filePath) {
      switch ($this -> checkChanges($filePath)) {
        case 0:
        array_push($this -> checkResult,[$filePath,'UNCHANGED']);
        break;
        case 1:
        array_push($this -> checkResult,[$filePath,'MODIFIED']);
        break;
        case 2:
        array_push($this -> checkResult,[$filePath,'NEW']);
        break;
      }
    }
  }
  private function checkChanges($filePath){
    // print_r("## CHECKING SINGLE FILE: {$filePath} \n");
    // $md5_old = "";
    // try {
    @$md5_old = $this -> stats[$filePath][1];
    var_dump($md5_old);
    // } catch (Exception $e) {}
    $md5_new = md5_file($filePath);
    $this -> stats[$filePath] = $md5_new;
    // print_r("STATS (OLD|NEW): {$md5_old}|{$md5_new}");
    // print_r("\n");
    if($md5_old === NULL){
      return 2;//added
    }
    if($md5_new === $md5_old){
      return 0;//not modified
    }
    return 1;//modified
  }
  public function resultToFile($fname = 'result.csv'){
    $fhandle = fopen($fname,"w+");
    foreach ($this -> checkResult as $key => $line){
      // $record = explode(',',$line);
      // array_unshift($record,$key);
      fputcsv($fhandle,$line);
    }
    fclose($fhandle);
  }
}

?>
