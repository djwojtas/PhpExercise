<?php require 'header.php';?>

<?php
  if(empty($_GET['nazwa']))
  {
    echo 'Lista blogów:<br />';
    
    $blogs = glob('*', GLOB_ONLYDIR);
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
      
      echo '<a href="blog.php?nazwa=' . $blog . '">' . $blog . '</a><br />';
    }
  }
  else
  {
    $blog = glob($_GET['nazwa'], GLOB_ONLYDIR);
    if(empty($blog))
    {
      echo 'Brak bloga o podanej nazwie. <a href="blog.php">Wyświetl listę</a>';
    }
    else
    {
      $file = fopen($blog[0] . '/info', 'r') or die("Błąd przy otwieraniu bloga");
      if(flock($file,LOCK_SH))
      {
        $blogData = fgetcsv($file);
        flock($file,LOCK_UN);
      }
      else
      {
        echo "Błąd podczas lockowania pliku.";
      }
      fclose($file);
      
      echo 'Blog <strong>' . $blog[0] . '</strong> użytkownika ' . $blogData[0] . '<br />' .
            'Opis:<br />' .
            $blogData[2] . '<br /><br />';
      
      $files = glob($blog[0] . '/*');
      foreach ($files as $file)
      {
        if(preg_match('/.*\/((\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2}))$/', $file, $matches))
        {
          $match = array($file, $matches);
          $articles[] = $match; 
        }
      }
      foreach ($articles as $article)
      {
        echo '<hr><br />Artykuł z dnia ' .
          $article[1][2] . '-' .
          $article[1][3] . '-' .
          $article[1][4] . ' ' .
          $article[1][5] . ':' .
          $article[1][6] . ':' .
          $article[1][7] . '<br />';

        $contentFile = fopen($article[1][0], 'r') or die("Błąd przy otwieraniu zawartości artykułu");
        if(flock($contentFile,LOCK_SH))
        {
          while(!feof($contentFile))
          {
            echo fgets($contentFile) . "<br />";
          }
          flock($contentFile,LOCK_UN);
        }
        else
        {
          echo "Błąd podczas lockowania pliku.";
        }
        fclose($contentFile);
        
        $attachements = array_filter(glob($article[1][0] . '?.*'), 'is_file');
        $i=1;
        foreach($attachements as $attachement)
        {
          echo '<a href="' . $attachement . '">Załącznik nr.' . $i . '</a><br />';
          $i++;
        }
        
        if(file_exists($article[1][0] . '.k'))
        {
          echo '<br />Komentarze:<br /><br />';
          $commentFiles = glob($article[1][0] . '.k/*');
          
          foreach($commentFiles as $comment)
          {
            $commentFile = fopen($comment, 'r') or die("Błąd przy otwieraniu zawartości artykułu");
            if(flock($commentFile,LOCK_SH))
            {
              $commentType = (int)fgets($commentFile);
              echo 'Rodzaj komentarza: ';
              switch ($commentType)
              {
              case 0:
                  echo "pozytywny";
                  break;
              case 1:
                  echo "neutralny";
                  break;
              case 2:
                  echo "negatywny";
                  break;
              }
              $date = fgets($commentFile);
              $nick = fgets($commentFile);
              echo '<br /><strong>' . $nick . '</strong> dnia ' . $date . ' mówi:<br />';
              while(!feof($commentFile))
              {
                echo fgets($commentFile) . "<br />";
              }
              flock($commentFile,LOCK_UN);
            }
            else
            {
              echo "Błąd podczas lockowania pliku.";
            }
            fclose($commentFile);
            echo '<br />';
          }
        }
        
        echo '<form action="addComment.php" method="post">' .
          '<input type="hidden" name="blogName" value="' . $blog[0] . '">' .
          '<input type="hidden" name="article" value="' . $article[1][1] . '">' .
          '<input type="submit" name="submit" value="Skomentuj"></form>';
      }      
    }
  }
?>
