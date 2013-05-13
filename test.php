<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);
echo '<legend>Testing query __constructs</legend>';

$text = new Account(1);
echo $text->accountEmail();


include ('/layout/bot.php');

?>