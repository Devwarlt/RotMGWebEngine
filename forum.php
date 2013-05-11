<?PHP

include ('/layout/header.php');
check::CheckNotLog();

echo '<h2>'.$config['server_name'].' <small> Forum</small></h2>';
Display::getCategories();

include ('/layout/bot.php');

?>