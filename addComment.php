<?php require 'header.php';?>

<form action="koment.php" method="post">
  Rodzaj kometarza: 
  <select name="commentType">
    <option value="0">Pozytywny</option>
    <option value="2">Negatywny</option>
    <option value="1">Neutralny</option>
  </select><br />
  Pseudonim: <input type="text" name="nick"><br />
  <textarea name="comment">Treść komentarza</textarea><br />
  <input type="submit" value="Dodaj komentarz" name="submit"><br />
  <input type="reset" value="Wyczyść">
  <input type="hidden" name="blogName" value="<?php echo $_POST['blogName'] ?>">
  <input type="hidden" name="article" value="<?php echo $_POST['article'] ?>">
</form>
    
<?php require 'footer.php';?>