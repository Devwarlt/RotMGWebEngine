<?PHP

include ('/layout/header.php');

echo '<legend>Monster : <b>'.$_GET['name'].'</b></legend>';

Display::loadMonster($_GET['name']);


include ('/layout/bot.php');

?>