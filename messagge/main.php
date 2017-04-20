<?php


	if (isset($_POST["submit"])) {
		$name = $_POST['name'];
		$email = $_POST['email'];
		$message = $_POST['message'];
		$human = intval($_POST['human']);
		//$from = 'Demo Contact Form';
		//$to = 'example@domain.com';
		//$subject = 'Message from Contact Demo ';

		//$body ="From: $name\n E-Mail: $email\n Message:\n $message";
		// Check if name has been entered
		if (!$_POST['name']) {
			$errName = 'Please enter your name';
		}

		// Check if email has been entered and is valid
		if (!$_POST['email'] || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$errEmail = 'Please enter a valid email address';
		}

		//Check if message has been entered
		if (!$_POST['message']) {
			$errMessage = 'Please enter your message';
		}
		//Check if simple anti-bot test is correct
		if ($human !== 5) {
			$errHuman = 'Your anti-spam is incorrect';
		}
// If there are no errors, send the email
//if (!$errName && !$errEmail && !$errMessage && !$errHuman) {
//	if (mail ($to, $subject, $body, $from)) {
//		$result='<div class="alert alert-success">Thank You! I will be in touch</div>';
//	} else {
//		$result='<div class="alert alert-danger">Sorry there was an error sending your message. Please try again later.</div>';
//	}
}
	//}
  // Собственно название функции говорит за себя.
  // Обычно не в моих правилах обрабатывать текст до добавления в базу,
  // но в этом случае упор на простоту гостевухи, а не на понты.
  function text2html($message) {
      // от функции nl2br отказался из соображений:
      // "всё равно без str_replace не обойтись так какого спрашивается хера."
      // была даже мысль без htmlspecialchars даже обойтись...
      return str_replace(
          array("\r\n", "\r", "\n", "\t"),
          array('<br>', '<br>', '<br>', '&nbsp; &nbsp; &nbsp;'),
          htmlspecialchars(trim($message))
      );
  }

  // Проверка была ли отправлена форма
  if (array_key_exists('message', $_POST)) {
      //проверка есть ли текст сообщения
      if (trim($_POST['message']) != '') {
          // Проверка (и убирание) вселенского зла (волшебных кавычек)
          $_POST['message'] = get_magic_quotes_gpc() ? stripslashes($_POST['message']) : $_POST['message'];
          // проверка имени (в противном случае "Anonymous")
          $_POST['name'] = array_key_exists('name', $_POST) && trim($_POST['name']) != '' ? $_POST['name'] : 'Anonymous';
          // Проверка (и убирание) вселенского зла (волшебных кавычек)
          $_POST['name'] = get_magic_quotes_gpc() ? stripslashes($_POST['name']) : $_POST['name'];
          // обработка поста для записи в БД. (хоть и не особо в моём стиле, но "третий сорт - не брак")
          $post = sprintf("<b>%s</b> - <i>%s</i><br>%s\n",
              text2html($_POST['name']),
              gmdate('r'),
              text2html($_POST['message'])
          );
          // Добавление поста в базу.
          // (функция file_put_contents появилась только в РНР5, по этому на РНР4 гостевуха работать не будет)
          // Думаю излишне напоминать что на *nix системах файл БД должен существовать и быть доступным для записи.
          file_put_contents('guest.txt', $post, FILE_APPEND);
          // Запоминание ника в куках
          setcookie("name", $_POST['name'], time()+31536000);
      }
      // так как форма была отправлена, редирект на первую страницу.
      header('Location: ./main.php');
      exit;
  }

  // Чтение в память всей БД
  // (при не особо большой базе тормозов особо не будет)
  $posts = file('guest.txt');

  // Сортировка (чтоб новые были сверху)
  krsort($posts);

  // подсчёт постов
  $posts_num = count($posts);

  // кол-во постов на страницу
  $pp = 10;

  // заметьте при получении номера текущей страницы только проверка никаких фильтраций
  // и тем более конверсий. Подробности: http://dkflbk.nm.ru/php_basic_err_1.html
  $start = isset($_GET['start']) && ctype_digit($_GET['start']) ? $_GET['start'] : 0;

  // собственно обрезание ненужного
  $posts = array_slice($posts, $start, $pp);

  // проверка запомненного имени (в противном случае "Anonymous")
  $_COOKIE['name'] = array_key_exists('name', $_COOKIE) && trim($_COOKIE['name']) != '' ? $_COOKIE['name'] : 'Anonymous';

  // Проверка (и убирание) вселенского зла (волшебных кавычек)
  $_COOKIE['name'] = get_magic_quotes_gpc() ? stripslashes($_COOKIE['name']) : $_COOKIE['name'];


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bootstrap contact form with PHP example">
    <meta name="author" content="MaxxRus">
    <title>Bootstrap Contact Form With PHP Example</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.css">
  </head>
  <body>
  	<div class="container">
  		<div class="row">
  			<div class="col-md-6 col-md-offset-3">
          <div class="list-group">
            <h1 class="page-header text-center">Комментарии</h1>
            <hr>
            <p> Страницы:
    <?php for($i = 0; $i < $posts_num; $i += $pp):?>
                &lt;<a href="?start=<?php echo $i;?>"><?php echo ($i+1) ."-" . ($i+$pp);?></a>&gt;
    <?php endfor;?>
            </p>
            <hr>
    <?php foreach($posts as $post):?>
            <p><?php echo trim($post);?></p><hr>
    <?php endforeach;?>
            <p> Страницы:
    <?php for($i = 0; $i < $posts_num; $i += $pp):?>
                &lt;<a href="?start=<?php echo $i;?>"><?php echo ($i+1) ."-" . ($i+$pp);?></a>&gt;
    <?php endfor;?>
            </p>
            <hr>

          </div>
  				<h1 class="page-header text-center">Оставте свой коментарий</h1>
				<form class="form-horizontal" role="form" method="post" action="main.php">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="name" name="name" placeholder="First & Last Name" value="<?php echo htmlspecialchars($_POST['name']); ?>">
							<?php echo "<p class='text-danger'>$errName</p>";?>
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-2 control-label">Email</label>
						<div class="col-sm-10">
							<input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="<?php echo htmlspecialchars($_POST['email']); ?>">
							<?php echo "<p class='text-danger'>$errEmail</p>";?>
						</div>
					</div>
					<div class="form-group">
						<label for="message" class="col-sm-2 control-label">Message</label>
						<div class="col-sm-10">
							<textarea class="form-control" rows="4" name="message"><?php echo htmlspecialchars($_POST['message']);?></textarea>
							<?php echo "<p class='text-danger'>$errMessage</p>";?>
						</div>
					</div>
					<div class="form-group">
						<label for="human" class="col-sm-2 control-label">2 + 3 = ?</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="human" name="human" placeholder="Your Answer">
							<?php echo "<p class='text-danger'>$errHuman</p>";?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<input id="submit" name="submit" type="submit" value="Send" class="btn btn-primary">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-10 col-sm-offset-2">
							<?php echo $result; ?>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
  </body>
</html>
