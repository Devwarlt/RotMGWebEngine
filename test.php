<?PHP

include ('/layout/header.php');

Check::checkAdminStatus($_SESSION['user']);
echo '<legend>Testing query __constructs</legend>';

$stats  = new Stats(6);
echo $stats->accountCredits();

include ('/layout/bot.php');

?>