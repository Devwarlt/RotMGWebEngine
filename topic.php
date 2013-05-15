<?PHP

include ('/layout/header.php');

check::CheckNotLog();

Display::getPost($_GET['id']);


include ('/layout/bot.php');

?>