<?php require 'header.php';?>

<?php
  if(isset($_POST['submit']) && !empty($_POST['blogName']) && !empty($_POST['userName']) && !empty($_POST['password']))
  {
    if(!is_dir($_POST['blogName']))
    {
      $sem=sem_get(3);
      sem_acquire($sem);
      
      $blogs = glob('*', GLOB_ONLYDIR);
      $create_flag = true;
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
          $create_flag = false;
          sem_release($sem);
          break;
        }
      }

      if($create_flag)
      {
        mkdir($_POST['blogName'], 0755); 
        sem_release($sem);   
        $infos = array($_POST['userName'], md5($_POST['password']), $_POST['blogDesc']);
        
        $file = fopen($_POST['blogName'] . '/info', 'w') or die("Błąd przy tworzeniu pliku");
        if(flock($file,LOCK_EX))
        {
          fputcsv($file, $infos);
          flock($file,LOCK_UN);
        }
        else
        {
          echo "Błąd podczas lockowania pliku.";
        }
        fclose($file);

        @chmod($file, 0755);
      }
      else
      {
        echo 'Użytkownik o podanej nazwie posiada już bloga';
      }

    }
    else
    {
      echo 'Blog o takiej nazwie istnieje, nie tworzę.';
    }
  }
  else
  {
    echo 'Pola nie mogą być puste';
  }
?>

<?php require 'footer.php';?>
