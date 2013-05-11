<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);

echo '<form action="" method="POST">
<input type="text" name="title" placeholder="News title"><br>
<input type="text" name="desc" placeholder="News short description"><br>
<textarea name="text"> News body ( feel free to use tags ) </textarea><br>
<button type="submit" value="submit" name="submit" class="btn btn-primary">Submit<i class="icon-ok icon-white"></i></button>
</form>';

if ($_POST['submit']) {

	$title = $_POST['title'];
	$desc = $_POST['desc'];
	$text = $_POST['text'];

	if ($title && $desc && $text) {

		$link = 'a';

		Execute::addNews($title,$desc,$text,$link);

	} else {

		Display::AlertError('Fill all fields !');

	}

}

include ('/layout/bot.php');

?>