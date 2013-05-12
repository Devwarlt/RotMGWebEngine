<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);

include_once('itemcreator.php');

include ('/layout/bot.php');

?>