<?PHP

	include ('/layout/header.php');
	unset($_SESSION['loged']);
	unset($_SESSION['name']);
    header("Location: index.php");
    include ('/layout/bot.php');
   

?>