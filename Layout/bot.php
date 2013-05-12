</div>
<div class="well"><?PHP $starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];
$endtime = microtime();
$endarray = explode(" ", $endtime);
$endtime = $endarray[1] + $endarray[0];
$totaltime = $endtime - $starttime; 
$totaltime = round($totaltime,5);
echo 'Load time: '.$totaltime.' seconds'; ?> | RotMGWebEngine - <a href="credits.php">Credits</a></div>
   </div>
   </div>
  </body>
</html>

          