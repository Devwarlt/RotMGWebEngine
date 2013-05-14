<?PHP

include ('/layout/header.php');
check::CheckLog();
echo '
<form action="login.php" method="POST">
  <fieldset>
    <legend>Login to '.$config['server_name'].'</legend>
    <input type="text" placeholder="Email" name="email"><br>
    <input type="password" placeholder="Password" name="password"><br>
    <button type="submit" value="submit" name="submit" class="btn btn-primary">Submit <i class="icon-ok icon-white"></i></button>
  </fieldset>
</form>';

if (isset($_GET['error']) == 'loginfirst') {

	Display::AlertError('Please log in first !');

} else {

	if (isset($_POST['submit'])) {

		$username = $_POST['email'];
		$password = sha1($_POST['password']);

		if ($username && $password) {

			if (check::CheckPassword($username,$password) == false) {

				$getname = Database::$db->prepare('SELECT `name` FROM `accounts` WHERE `uuid` = :uuid');
				$getname->bindParam(':uuid',$username,PDO::PARAM_STR);

				if ($getname->execute()) {

					$user = $getname->fetch(PDO::FETCH_ASSOC);

					$_SESSION['loged'] = 1;
					$_SESSION['user'] = $user['name'];
					header("Location: myaccount.php");
				}

			} else {

				Display::AlertError('Wrong login details ! ');

			}

		} else {

			Display::AlertError('Fill all fields ! ');

		}

	}
}

include ('/layout/bot.php');

?>