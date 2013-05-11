<?PHP

include ('/layout/header.php');
check::CheckNotLog();

echo '<h2>Welcome <small>'.$_SESSION['user'].' !</small></h2>';

Display::getAccountInformation($_SESSION['user']);

if (Check::checkAdmin($_SESSION['user']) == true) {

	echo '<a href="logout.php">[ Logout ] </a> <a href="adminnews.php">[ Admin news system ]</a>';

} else {

	echo '<a href="logout.php">[ Logout ]</a>';

}

include ('/layout/bot.php');

?>