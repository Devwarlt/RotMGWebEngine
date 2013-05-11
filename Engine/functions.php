<?php 

	//Class to connect to DB and display news on index.php and install acc

	class Database {
		
		public static $db;

		public static function connectme () {

			include('/config/config.php');

			self::$db = new PDO("mysql:host={$connect['host']};dbname={$connect['database']}", $connect['user'], $connect['password']);
			
		}
	}

	Database::connectme();

	class Display {

		public static function News() {

			$news = Database::$db->prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 5");

			if ($news->execute()) {

				$display = $news->fetchAll(PDO::FETCH_ASSOC);
				foreach ($display as $new) {

					echo '<table class="table table-striped"><thead><tr><th><a href="news.php?id='.$new['id'].'">'.$new['title'].'</a></th></tr></thead>
					<tbody><tr><td>'.$new['fulltext'].'</td></tr><tr><td>'.$new['date'].'</td></tr></tbody>
					</table>';
				}

			}
		}

		public static function editNews($id) {

			$getinfo = Database::$db->prepare('SELECT * FROM `news` WHERE `id` = :id');
			$getinfo->bindParam(':id',$id,PDO::PARAM_INT);

			if ($getinfo->execute()) {

				$info = $getinfo->fetch(PDO::FETCH_ASSOC);
				echo'<form action="" method="POST">Title : <input type="text" name="title" value="'.$info['title'].'"><br>
				Desc : <input type="text" name="desc" value="'.$info['text'].'"><br>
				<textarea id="redactor" name="fulltext">'.$info['fulltext'].'</textarea><br><button type="submit" value="submit" name="submit" class="btn btn-primary">Submit <i class="icon-ok icon-white"></i></button><br><form><br>';

				if ($_POST['submit']) {

					$title = $_POST['title'];
					$desc = $_POST['desc'];
					$fulltext = $_POST['fulltext'];

					if ($title && $desc && $fulltext) {

						$edit = Database::$db->prepare('UPDATE `news` SET `title` = :title, `text` = :descc, `fulltext` = :textt WHERE `id` = :id');
						$edit->bindParam(':title',$title,PDO::PARAM_STR);
						$edit->bindParam(':descc',$desc,PDO::PARAM_STR);
						$edit->bindParam(':textt',$fulltext,PDO::PARAM_STR);
						$edit->bindParam(':id',$id,PDO::PARAM_INT);

						if ($edit->execute()) {

							self::AlertSuccess('News edited !');

						}

					} else {

						self::AlertError('Fill all fields !');

					}
				}
			}
		}

		public static function removeNews($id) {

			$remove = Database::$db->prepare('DELETE FROM `news` WHERE `id` = :id');
			$remove->bindParam(':id',$id,PDO::PARAM_INT);

			if ($remove->execute()) {

				self::AlertSuccess('News removed !');

			}
		}

		public static function getNews($id) {

			$getshit = Database::$db->prepare("SELECT * FROM `news` WHERE `id` = :id");
			$getshit->bindParam(':id',$id,PDO::PARAM_INT);

			if ($getshit->execute()) {

				if ($getshit->rowCount() == 0) {

					self::AlertError('Wrong ID');


				} else {

					$info = $getshit->fetch(PDO::FETCH_ASSOC);


					echo '<h3>'.$info['title'].'</h3>
					<br>'.$info['fulltext'].'';

				}

			}
		}

		public static function getAllNews() {

			$getinfo = Database::$db->prepare('SELECT * FROM `news` ORDER BY `id` DESC');

			if ($getinfo->execute()) {

				$info = $getinfo->fetchAll(PDO::FETCH_ASSOC);
				echo '<table class="table table-bordered"><tr><td><center>Edit</center></td><td><center>Remove</center></td><td><center>Title</center></td></tr>';	

				foreach ($info as $display) {

					echo '<tr><td><center><a href="edit.php?id='.$display['id'].'"><i class="icon-edit"></i></a></center></td><td><center><a href="remove.php?id='.$display['id'].'"><i class="icon-remove"></i></a></center></td><td><center>'.$display['title'].'</center></td></tr>';
				}

				echo '</table>';
			}
		}

		public static function getAccountInformation($name) {

			$getaccountid = Database::$db->prepare('SELECT `id`,`name` FROM `accounts` WHERE `name` = :name');
			$getaccountid->bindParam(':name',$name,PDO::PARAM_STR);

			if ($getaccountid->execute()) {

				$id = $getaccountid->fetch(PDO::FETCH_ASSOC);
				
				$getfamecredits = Database::$db->prepare('SELECT `fame`,`credits`,`accId` FROM `stats` WHERE `accId` = :id');
				$getfamecredits->bindParam(':id',$id['id'],PDO::PARAM_INT);

				if ($getfamecredits->execute()) {

					$info = $getfamecredits->fetch(PDO::FETCH_ASSOC);

					echo 'You currently have : <b>'.$info['fame'].' </b> Fame<br>
					You currently have : <b>'.$info['credits'].'</b> Credits<br><br>';

				}
			}
		}

		public static function AlertError($text) {

			echo '<div class="alert alert-error"><b>Following errors occurred : </b> '.$text.'  </div>';

		}

		public static function AlertSuccess($text) {

			echo '<div class="alert alert-success"><b>Congratulations : </b> '.$text.'  </div>';

		}
	}

	class Execute {

		public static function addNews($title,$text,$fulltext,$link) {

			include('config/config.php');

			$add = Database::$db->prepare('INSERT INTO `news` (`icon`,`title`,`text`,`fulltext`,`link`) VALUES (1,:title,:textt,:fulltext,:link)');
			$add->bindParam(':title',$title,PDO::PARAM_STR);
			$add->bindParam(':textt',$text,PDO::PARAM_STR);
			$add->bindParam(':fulltext',$fulltext,PDO::PARAM_STR);
			$add->bindParam(':link',$link,PDO::PARAM_STR);

			if ($add->execute()) {

				Display::AlertSuccess('News added !');
				$id = Database::$db->lastInsertId();
				$linky = $config['server_website'].'/news.php?id='.$id;
				$editlink = Database::$db->prepare('UPDATE `news` SET `link` = :link WHERE `title` = :title');
				$editlink->bindParam(':link',$linky,PDO::PARAM_STR);
				$editlink->bindParam(':title',$title,PDO::PARAM_STR);

				if ($editlink->execute()) {



				}
			}
		}
	}

	class Check {

		public static function checkNews($id) {

			$check = Database::$db->prepare('SELECT `id` FROM `news` WHERE `id` = :id');
			$check->bindParam(':id',$id,PDO::PARAM_INT);

			if ($check->execute()) {

				if ($check->rowCount() == 0) {

					Display::AlertError('Wrong news ID !');
					return false;
				}
			}
		}

		public static function checkEmail($emaill) {

			$email = Database::$db->prepare('SELECT `uuid` FROM `accounts` WHERE `uuid` = :em');
			$email->bindParam(':em',$emaill,PDO::PARAM_STR);

			if ($email->execute()) {

				if ($email->rowCount() == 0) {

					return true;

				} else {

					return false;

				}
			}
		}

		public static function checkUser($userr) {

			$user = Database::$db->prepare('SELECT `name` FROM `accounts` WHERE `name` = :em');
			$user->bindParam(':em',$userr,PDO::PARAM_STR);

			if ($user->execute()) {

				if ($user->rowCount() == 0) {

					return true;

				} else {

					return false;

				}
			}
		}

		public static function checkAdminStatus($name) {

			$getadmin = Database::$db->prepare('SELECT `admin`,`name` FROM `accounts` WHERE `name` = :name AND `admin` = 1');
			$getadmin->bindParam(':name',$name,PDO::PARAM_STR);

			if ($getadmin->execute()) {

				if ($_SESSION['loged'] != 1) {

					header("Location: index.php");
				}

				if ($getadmin->rowCount() == 0) {

					header("Location: index.php");
				}
			}
		}

		public static function checkAdmin($name) {

			$getadmin = Database::$db->prepare('SELECT `admin`,`name` FROM `accounts` WHERE `name` = :name AND `admin` = 1');
			$getadmin->bindParam(':name',$name,PDO::PARAM_STR);

			if ($getadmin->execute()) {

				if ($getadmin->rowCount() > 0) {

					return true;

				} else {

					return false;

				}
			}
		}

		public static function checkPassword($email,$pw) {

			$user = Database::$db->prepare('SELECT `password`,`uuid` FROM `accounts` WHERE `uuid` = :em AND `password` = :pw');
			$user->bindParam(':em',$email,PDO::PARAM_STR);
			$user->bindParam(':pw',$pw,PDO::PARAM_STR);

			if ($user->execute()) {

				if ($user->rowCount() == 0) {

					return true;

				} else {

					return false;

				}
			}
		}

		public static function checkLog() {

			if ($_SESSION['loged'] == 1) {

				header("Location: /myaccount.php");

			}

		}

		public static function checkNotLog() {

			if ($_SESSION['loged'] == 0) {

				header("Location: /login.php");

			}

		}
	}




	