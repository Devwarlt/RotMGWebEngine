<?PHP
session_Start();
error_reporting(-1);
include ('/config/config.php');
include ('/engine/functions.php');
Protect::protectPage();
?>
<html>
  <head>
    <title><?PHP echo $config['server_name']; ?></title>
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="layout/css.css" rel="stylesheet">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>   
    <link rel="stylesheet" href="/redactor/redactor/redactor.css" />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <script src="/redactor/redactor/redactor.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            //$('.dropdown-toggle').dropdown();
            $('.redactor').redactor();
            //$(".alert").alert();
        });
  </script>

  </head>
  <body>
   <div class="navbar navbar-inverse navbar-static-top">
     <div class="navbar-inner">
        <div class="container">
        <a class="brand" href="#"><?PHP echo $config['server_name']; ?></a>
          <ul class="nav">
                  <li><a href="/index.php"><i class="icon-home icon-white"></i> <b>Home</b></a></li>

                  <?PHP

                  if (isset($_SESSION['loged']) == 1) {

                    echo '<li><a href="/myaccount.php"><i class="icon-user icon-white"></i> <b>Account</b></a></li>';

                  } else {

                  echo '<li><a href="/login.php"><i class="icon-user icon-white"></i> <b>Login</b></a></li>';

                  }

                  ?>
                  <li><a href="/about.php"><i class="icon-book icon-white"></i> <b>About</b></a></li>
                  <li class="dropdown">
                      <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-white icon-bullhorn"></i> <b>Community</b> <b class="caret"></b></a>
                      <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                        <li><a tabindex="-1" href="forum.php">Forum</a></li>
                        <li><a tabindex="-1" href="topfame.php">Top fame</a></li>
                        <li><a tabindex="-1" href="deaths.php">Lastest deaths</a></li>
                      </ul>
                  </li>  
                  <li class="dropdown">
                      <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-white icon-folder-open"></i> <b>Library</b> <b class="caret"></b></a>
                      <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                        <li><a tabindex="-1" href="monsters.php">Monsters</a></li>
                        <li><a tabindex="-1" href="items.php">Items</a></li>
                      </ul>
                  </li>  
          </ul>      
        </div>
      </div>
    </div>


    <div id="MainDiv">
      <div class="page-header">
      
    </div>
      <div id="SecondDiv">
        <div class="well"><h2><?PHP echo $config['server_name']; ?> <small><?PHP echo $config['server_desc']; ?></small></h2></div>
        <div class="well">

