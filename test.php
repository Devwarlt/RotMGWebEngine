<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);
echo '<legend>Testing query __constructs</legend>';


include ('/layout/bot.php');

?>