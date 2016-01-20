<?php
/**
* FileSupervisor Class PHP 5.3+
* recursive search
*/
class FileSupervisor
{
  protected $statusFileName = NULL;
  protected $fileNameRegExps = NULL;
  protected $checkResult = [];
  protected $stats = [];

  function __construct($statusFileName,$fileNameRegExps)
  {
    $this -> statusFileName = $statusFileName;
    $this -> fileNameRegExps = $fileNameRegExps;
    $this -> checkResult = [];
    $this -> stats = [];
    $stats = [];
    if (($statsHandler = fopen ($this -> statusFileName,"r")) !== FALSE) {
      while(!feof($statsHandler)){
        $stats[] = fgetcsv($statsHandler);
      }
      fclose($statsHandler);
    }
    foreach ($stats as $row) {
      $this -> stats[$row[0]] = $row;
    }
  }
  function runCheck(){
    foreach ($this -> fileNameRegExps as $fileRegExp) {
      // $matchedFiles = glob($fileRegExp);
      $matchedFiles = $this->rsearch($fileRegExp->path,"/{$fileRegExp->file}/");
      print_r($matchedFiles);
      $this -> checkFilesByName($matchedFiles);
    }
    $statsHandler = fopen($this -> statusFileName,"w+");
    foreach ($this -> stats as $key => $line){
      @$record = explode(',',$line);
      @array_unshift($record,$key);
      @fputcsv($statsHandler,$record);
    }
    fclose($statsHandler);
  }
  private function checkFilesByName($filePaths){
    foreach ($filePaths as $filePath) {
      $filePath = realpath($filePath);
      var_dump($filePath);
      if(!is_file($filePath)){
        continue;
      }
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
    @$md5_old = $this -> stats[$filePath][1];
    var_dump($md5_old);
    $md5_new = md5_file($filePath);
    $this -> stats[$filePath] = $md5_new;
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
      fputcsv($fhandle,$line);
    }
    fclose($fhandle);
  }
  private function rsearch($folder, $pattern) {
    $folder = realpath($folder);
    echo "RESEARCH: {$folder} FOR {$pattern}";
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    $parent = realpath($folder.'/..');
    echo $parent;
    foreach($files as $file) {
      $real = realpath($file[0]);
      if($real == $parent)continue;
      $fileList[] = $real;
    }
    return $fileList;
  }
}
?>
