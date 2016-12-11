<?php require 'header.php';?>

<?php
  if(isset($_POST['submit']) && 
     isset($_POST['commentType']) && 
     !empty($_POST['nick']) && 
     !empty($_POST['comment']) && 
     !empty($_POST['blogName']) && 
     !empty($_POST['article']))
  {
    echo 'Dodaję komentarz.';
    
    $dirPattern = './' . $_POST['blogName'] . '/' . $_POST['article'] . '.k';
    
    $existingCommentsFolder = glob($dirPattern, GLOB_ONLYDIR);
    if(empty($existingCommentsFolder))
    {
      @mkdir($dirPattern, 0755);
    }
    
    $commentsPattern = $dirPattern . '/*';
    $sem=sem_get(2);
    sem_acquire($sem);
    $existingComments = glob($commentsPattern);
    $uniqNum = empty($existingComments) ? 0 : count($existingComments);
    
    $file = fopen($dirPattern . '/' . $uniqNum, 'w') or die("Błąd przy tworzeniu pliku");
    if(flock($file,LOCK_EX))
    {
      fwrite($file,
           $_POST['commentType'] . "\n" .
           date("Y-m-d, h:i:s") . "\n" .
           $_POST['nick'] . "\n" .
           $_POST['comment']
           );
      flock($file,LOCK_UN);
    }
    else
    {
      echo "Błąd podczas lockowania pliku.";
    }
    fclose($file);
    @chmod($file, 0755);
    sem_release($sem);
  }
  else
  {
    echo 'Wystąpił błąd podczas zapisu komentarza';
  }
?>

<?php require 'footer.php';?>
