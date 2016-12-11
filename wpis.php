<?php require 'header.php';?>

<?php
  $blogs = glob('*', GLOB_ONLYDIR);
    
  $found_blog = null;
  foreach($blogs as $blog)
  {
    $search = fopen($blog . '/info', 'r') or die("Błąd przy przeszukiwaniu");
    if(flock($search,LOCK_SH))
    {
      $blogData = fgetcsv($search);
      flock($search,LOCK_UN);
    }
    else
    {
      echo "Błąd podczas lockowania pliku.";
    }
    fclose($search);
    
    if($blogData[0] == $_POST['userName'])
    {
      fclose($search);
      $found_blog = $blogData;
      $found_blog[3] = $blog;
      break;
    }
    
  }

  if($found_blog == null)
  {
    echo 'Brak bloga dla użytkownika o podanym nicku';
  }
  else
  {
    if($found_blog[1] != md5($_POST['password']))
    {
      echo 'Złe hasło.';
    }
    else
    {
      echo 'Dodaję wpis.';
      
      $fileName = str_replace('-', "", $_POST['date']) . str_replace(':', "", $_POST['time']) . date('s');
      $pattern = './' . $found_blog[3] . '/' . $fileName . '*';
      
      $sem=sem_get(4);
      sem_acquire($sem);
      $existingComments = glob($pattern);
      $uniqueNum = 0;
      if(!empty($existingComments))
      {
        $uniqueNum = count($existingComments);
      }
      $fileName = $fileName . sprintf("%02d", $uniqueNum);
      
      $file = fopen($found_blog[3] . '/' . $fileName , 'w') or die("Błąd przy tworzeniu pliku");
      if(flock($file,LOCK_EX))
      {
        fwrite($file, $_POST['article']);
        flock($file,LOCK_UN);
      }
      else
      {
        echo "Błąd podczas lockowania pliku.";
      }
      fclose($file);
      sem_release($sem);
      @chmod($filename, 0755);
      
      for($i=1; $i<=3; $i++)
      {
        $uploadedFile = "file" . $i;
        
        if(file_exists($_FILES[$uploadedFile]['tmp_name']) && is_uploaded_file($_FILES[$uploadedFile]['tmp_name']))
        {
          $filename = $_FILES[$uploadedFile]['name'];
          $ext = pathinfo($filename, PATHINFO_EXTENSION);
          $target_file = $found_blog[3] . '/' . $fileName . $i . '.' . $ext;
          
          if(move_uploaded_file($_FILES[$uploadedFile]["tmp_name"], $target_file))
          {
            echo "<br />Plik ". basename( $_FILES[$uploadedFile]["name"]). " zuploadowany<br />";
          }
          else
          {
            echo "Błąd podczas uploadu.";
          }
        }
      }
    }
  }
?>

<?php require 'footer.php';?>
