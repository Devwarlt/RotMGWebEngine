<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);
echo '<legend>Item XML editor</legend>';
include_once('itemcreator.php');

include ('/layout/bot.php');

?>