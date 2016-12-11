<?php require 'header.php';?>

<form action="wpis.php" method="post" enctype="multipart/form-data">
  Nazwa użytkownika: <input type="text" name="userName"><br />
  Hasło: <input type="password" name="password"><br />
  <textarea name="article">Wpis</textarea><br />
  Data: <input type="text" name="date" value="<?php echo date("Y-m-d") ?>"><br />
  Godzina: <input type="text" name="time" value="<?php echo date("h:i") ?>"><br />
  Załącznik 1: <input type="file" name="file1"><br />
  Załącznik 2: <input type="file" name="file2"><br />
  Załącznik 3: <input type="file" name="file3"><br />
  <input type="submit" value="Wyślij wpis" name="submit"><br />
  <input type="reset" value="Wyczyść">
</form>

<?php require 'footer.php';?>