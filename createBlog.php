<?php require 'header.php';?>

<form action="nowy.php" method="post">
  Nazwa bloga: <input type="text" name="blogName"><br />
  Nazwa użytkownika: <input type="text" name="userName"><br />
  Hasło: <input type="password" name="password"><br />
  <textarea name="blogDesc">Opis bloga</textarea><br />
  <input type="submit" value="Utwórz blog" name="submit"><br />
  <input type="reset" value="Wyczyść">
</form>
  
<?php require 'footer.php';?>