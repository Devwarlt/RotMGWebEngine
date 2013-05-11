<?PHP

include ('/layout/header.php');

check::CheckNotLog();

if (check::checkForum($_GET['id']) == true) {

	Display::AlertError('Wrong forum item ID');	

} else {

	Display::getTopics($_GET['id']);

}


include ('/layout/bot.php');

?>