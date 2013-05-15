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

		public static function lastDeaths() {

			$getdeaths = Database::$db->prepare('SELECT * FROM `death` ORDER BY `time` DESC LIMIT 15');

			if ($getdeaths->execute()) {

				$deaths = $getdeaths->fetchAll(PDO::FETCH_ASSOC);
				echo '<table class="table table-striped"><thead><tr><td><b>Name</b></td><td><b>At level</b></td><td><b>Killed by</b></td><td><b>Time</b></td></tr></thead>';

				foreach ($deaths as $display) {

					$getcharinfo = Database::$db->prepare('SELECT `level` FROM `characters` WHERE `id` = :id');
					$getcharinfo->bindParam(':id',$display['chrId'],PDO::PARAM_INT);

					if ($getcharinfo->execute()) {

						$levels = $getcharinfo->fetch(PDO::FETCH_ASSOC);
						echo '<tr><td>'.$display['name'].'</td><td>'.$levels['level'].'</td><td><a href="seemonster.php?name='.$display['killer'].'">'.$display['killer'].'</a></td><td>'.$display['time'].'</td></tr>';

					}
				}

				echo '</table>';
			}
		}

		public static function topFame() {

			$getfames = Database::$db->prepare('SELECT * FROM `stats` ORDER BY `fame` DESC LIMIT 30');

			if ($getfames->execute()) {

				$info = $getfames->fetchAll(PDO::FETCH_ASSOC);
				echo '<table class="table table-striped"><thead><tr><td><b>Name</b></td><td><b>Fame</b></td><td><b>Credits</b></td></tr></thead>';

				foreach ($info as $display) {

					$getnames = Database::$db->prepare('SELECT `name` FROM `accounts` WHERE `id` = :id');
					$getnames->bindParam(':id',$display['accId'],PDO::PARAM_INT);

					if ($getnames->execute()) {

						$names = $getnames->fetch(PDO::FETCH_ASSOC);
						echo '
						<tr><td>'.$names['name'].'</td><td>'.$display['fame'].'</td><td>'.$display['credits'].'</td></tr>';

					}	
				}

				echo '</table>';
			}
		}

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

				if (isset($_POST['submit'])) {

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

		public static function serverItems() {

			include ('/config/config.php');
			echo '<table class="table table-striped"><thead><tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Description</b></td></tr></thead>';
			foreach (glob($config['item_directory']."*.xml") as $filename) {

				$xml = simplexml_load_file($filename);
			
				foreach ($xml->children() as $child) {

					if ($child->Class == 'Equipment') {

				  		echo '<tr><td>'.hexdec($child->attributes()->type).'</td><td>'.$child->attributes()->id.'</td><td>'.$child->Description.'</td></tr>';

				  	}

				}

				
		    }

				echo '</table>';	      
		}

		public static function loadMonster($name) {

			include ('/config/config.php');

			foreach (glob($config['item_directory']."*xml") as $file) {

				$xml = simplexml_load_file($file);	

				foreach ($xml->children() as $child) {

					if ($child->attributes()->id == $name) {

						$exi = true;
						echo 'Monster name : <b>'.$child->attributes()->id.'</b><br>
						Monster group : <b>'.$child->Group.'</b><br>
						Hitpoints : <b>'.$child->MaxHitPoints.'</b><br>
						Defense : <b>'.$child->Defense.'</b><br>
						Terrain : <b>'.$child->Terrain.'</b><br>
						Attack animation : <b>'.$child->Projectile->ObjectId.'</b><br>
						Damage : <b>'.$child->Projectile->Damage.'</b><br>
						Speed : <b>'.$child->Projectile->Speed.'</b><br>';
						break;
					
					} 
				}
			}

			if (!$exi) { self::AlertError('Unknown monster name !'); }
		}

		public static function serverMonsters() {

			include ('/config/config.php');
			echo '<table class="table table-striped"><thead><tr><td><b>Name</b></td><td><b>Hitpoints</b></td><td><b>Defense</b></td><td><b>Spawns in</b></td></tr></thead>';	
			foreach (glob($config['item_directory']."*.xml") as $filename) {

				$xml = simplexml_load_file($filename);
			
				foreach ($xml->children() as $child) {

					if ($child->Class == 'Character') {

						if (empty($child->Terrain)) {

							$terrain = 'Unknown';

						} else {

							$terrain = $child->Terrain;

						}

						if (empty($child->Defense)) {

							$defense = 'Unknown';

						} else {

							$defense = $child->Defense;

						}

						if (empty($child->MaxHitPoints)) {

							$hp = 'Unknown';

						} else {

							$hp = $child->MaxHitPoints;

						}

						$linkname = str_replace(" ", "",$child->attributes()->id);
						$linkor = strtolower($linkname);
				  	
				  		echo '<tr><td><a href="seemonster.php?name='.$child->attributes()->id.'">'.$child->attributes()->id.'</a></td><td>'.$hp.'</td><td>'.$defense.'</td><td>'.$terrain.'</td></tr>';

				  	}

				}
		    }

				echo '</table>';	  
		}

		public static function removeNews($id) {

			$remove = Database::$db->prepare('DELETE FROM `news` WHERE `id` = :id');
			$remove->bindParam(':id',$id,PDO::PARAM_INT);

			if ($remove->execute()) {

				self::AlertSuccess('News item removed !');

			}
		}


		public static function createItem($filename) {

			header('Content-type: text/xml');
			$xml = simplexml_load_file($filename);

			echo $xml->asXML();

		}

		public static function getCategories() {

			$categories = Database::$db->prepare('SELECT * FROM `forum` ORDER BY `id`');

			if ($categories->execute()) {

				$category = $categories->fetchAll(PDO::FETCH_ASSOC);

				foreach ($category as $display) {

					$getposts = Database::$db->prepare('SELECT COUNT(*) AS `POSTS` FROM `category_topic` WHERE `forum_id` =:id');
					$getposts->bindParam(':id',$display['id'],PDO::PARAM_INT);

					if ($getposts->execute()) {

						$post = $getposts->fetch(PDO::FETCH_ASSOC);

						echo '<br><table class="table-striped" width="100%"><tr><td><b><a href="forumsee.php?id='.$display['id'].'">'.$display['title'].'</a></b><br>'.$display['desc'].'</td><td width="15%">Posts : <b>'.$post['POSTS'].'</b></td></tr></table>';
					
					}
				}
			}
		}

		public static function getTopics($id) {

			$getopics = Database::$db->prepare('SELECT * FROM `category_topic` WHERE `forum_id` = :forum_id');
			$getopics->bindParam(':forum_id',$id,PDO::PARAM_INT);

			if ($getopics->execute()) {

				if ($getopics->rowCount() == 0) {

					self::AlertError('Invalid topic ID');

				} else {

					$topics = $getopics->fetchAll(PDO::FETCH_ASSOC);

					foreach ($topics as $display) {

						$getreplies = Database::$db->prepare('SELECT COUNT(*) as `MAX` FROM `forum_post` WHERE `category_id` = :id');
						$getreplies->bindParam(':id',$display['id'],PDO::PARAM_INT);

						if ($getreplies->execute()) {

							$reply = $getreplies->fetch(PDO::FETCH_ASSOC);

							echo '<br><table class="table-striped" width="100%"><tr><td><a href="topic.php?id='.$display['id'].'"><b>'.$display['title'].'</b></a><br>Started by : '.$display['owner'].' at '.$display['added'].'</td><td width="15%"><td>Replies : <b>'.$reply['MAX'].'</b></td></tr></table>';
						}
					}
				}
			}
		}

		public static function getPost($id) {

			$getposts = Database::$db->prepare('SELECT * FROM `forum_post` WHERE `category_id` = :id');
			$getposts->bindParam(':id',$id,PDO::PARAM_INT);

			if ($getposts->execute()) {

				if ($getposts->rowCount() == 0) {

					self::AlertError('Invalid post ID');

				} else {

					$posts = $getposts->fetchAll(PDO::FETCH_ASSOC);
					foreach ($posts as $display) {

						echo '<table class="table table-striped"><tr><thead><td>Posted by : <b>'.$display['owner'].'</b></td></thead></tr>';
						echo '<tr><td>'.$display['text'].'</td></tr>';
						echo '</table>';

					}
				}
			}
		}

		public static function getNews($id) {

			$getshit = Database::$db->prepare("SELECT * FROM `news` WHERE `id` = :id");
			$getshit->bindParam(':id',$id,PDO::PARAM_INT);

			if ($getshit->execute()) {

				if ($getshit->rowCount() == 0) {

					self::AlertError('Wrong news item ID');


				} else {

					$info = $getshit->fetch(PDO::FETCH_ASSOC);


					echo '<h3>'.$info['title'].'</h3>
					<br>'.$info['fulltext'].'';

				}

			}
		}

		public static function onlinePlayers() {

			$count = Database::$db->prepare('SELECT COUNT(*) as `TOTAL` FROM `characters` WHERE `online` = 1');

			if ($count->execute()) {

				$online = $count->fetch();

				if ($online['TOTAL'] == 0) {

					$number = 'Nobody is online';

				} else {

					$number = $online['TOTAL'];

				}

				echo $number;

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

	class Protect {

		public static function protectPage() {

			$ip = $_SERVER['REMOTE_ADDR'];
			$insertip = Database::$db->prepare('SELECT * FROM `visitors` WHERE `ip` = :ip');
			$insertip->bindParam(':ip',$ip,PDO::PARAM_STR);

			if ($insertip->execute()) {

				$ipp = $insertip->fetch(PDO::FETCH_ASSOC);
				$current = strtotime(date("H:i:s"));

				if ($ipp['time'] + 60 <= $current) {

					$clearloig = Database::$db->prepare('DELETE FROM `visitors` WHERE `ip` = :ip'); 
					$clearloig->bindParam(':ip',$ip,PDO::PARAM_STR);
					$clearloig->execute();

				}


				if ($ipp['block'] >= 10) {

					if  ($ipp['added'] != 1) {

						$addattack =  Database::$db->prepare('UPDATE `visitors` SET `added` = 1 WHERE `ip` = :ip');
						$addattack->bindParam(':ip',$ip,PDO::PARAM_STR);
						$addattack->execute();
						$filename = 'attack.txt';
						$handle = fopen($filename, 'a');
						$informationattack = ' IP : '.$ip.' OVERLOADED LIMIT AT  '.date("Y-m-d H:i:s");
						fwrite($handle, "\n".$informationattack."\n");
						fclose($handle);

					} else {

					die('LOAD LIMIT | WAIT 60 SECONDS !');

					}

				} else {

					if ($insertip->rowCount() == 0) {

						$add = strtotime(date("H:i:s"));

						$addlog = Database::$db->prepare('INSERT INTO `visitors` (`ip`,`time`) VALUES (:ip,:timee)');
						$addlog->bindParam(':ip',$ip,PDO::PARAM_STR);
						$addlog->bindParam(':timee',$add,PDO::PARAM_STR);
						$addlog->execute();

					} else {

						$addd = strtotime(date("H:i:s"));

						$addblock = Database::$db->prepare('UPDATE `visitors` SET `block` = `block` + 1 WHERE `ip` = :ip');
						$addblock->bindParam(':ip',$ip,PDO::PARAM_STR);
						$addblock->execute();
					}
				}
			}
		}
	}

	class Check {

		public static function checkForum($idd) {

			$forum = Database::$db->prepare('SELECT `id` FROM `forum` WHERE `id` = :em');
			$forum->bindParam(':em',$idd,PDO::PARAM_INT);

			if ($forum->execute()) {

				if ($forum->rowCount() == 0) {

					return true;

				} else {

					return false;

				}
			}
		}

		public static function checkNews($id) {

			$check = Database::$db->prepare('SELECT `id` FROM `news` WHERE `id` = :id');
			$check->bindParam(':id',$id,PDO::PARAM_INT);

			if ($check->execute()) {

				if ($check->rowCount() == 0) {

					Display::AlertError('Wrong news item ID !');
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

			if (isset($_SESSION['loged']) == 1) {

				header("Location: /myaccount.php");

			}

		}

		public static function checkNotLog() {

			if (isset($_SESSION['loged']) == 0) {

				header("Location: /login.php?error=loginfirst");

			}

		}
	}

	Class Stats {

		public $info;

		public function __construct($id) {

			$getinfo = Database::$db->prepare('SELECT * FROM `stats` WHERE `accId` = :id');
			$getinfo->bindParam(':id',$id,PDO::PARAM_INT);

			if ($getinfo->execute()) {

				$this->info = $getinfo->fetch(PDO::FETCH_ASSOC);

			}
		}

		public function accountFame() {

			$fame = $this->info['fame'];
			return $fame;

		}

		public function accountCredits() {

			$credits = $this->info['credits'];
			return $credits;

		}

		public function accountTotalFame() {

			$totalf = $this->info['totalFame'];
			return $totalf;

		}

		public function accountTotalCredits() {

			$totalc = $this->info['totalCredits'];
			return $totalc;
		}
	}

	Class Account {

		public $info;

		public function __construct($id) {

			$getinfo = Database::$db->prepare('SELECT * FROM `accounts` WHERE `id` = :id');
			$getinfo->bindParam(':id',$id,PDO::PARAM_INT);

			if ($getinfo->execute()) {

				$this->info = $getinfo->fetch(PDO::FETCH_ASSOC);

			}
		}

		public function accountId() {

			$id = $this->info['id'];
			return $id;

		}

		public function accountEmail() {

			$email = $this->info['uuid'];
			return $email;
		}

		public function accountName() {

			$name = $this->info['name'];
			return $name;
		}

		public function accountPassword() {

			$password = $this->info['password'];
			return $password;
		}

		public function accountRegTime() {

			$time = $this->info['regTime'];
			return $time;
		}

	}




	