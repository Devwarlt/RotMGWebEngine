<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);

echo '<a href="addnews.php">[ Add news ]</a><br><br>';

Display::getAllNews();

include ('/layout/bot.php');

?>