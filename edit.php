<?PHP

include ('/layout/header.php');
Check::checkNews($_GET['id']);
Check::checkAdminStatus($_SESSION['user']);

Display::editNews($_GET['id']);


include ('/layout/bot.php');

?>