<?php
  $handle = fopen("files/text.txt", "at");

  $string = "This is text";
  $test = fwrite($handle, $string);

  if ($test) echo 'Данные в файл успешно занесены.';
  else echo 'Ошибка при записи в файл.';
  fclose($handle);

?>
<!-- После запуска этого скрипта, в файле text.txt добавится строка "This is text".
Если ошыбка при записи то проблема доступа...
 Попробуйте поставить if (!is_writable("somenewfile.txt")) die('not writable');-->
