<?php
	require_once('config.php');
	require_once('createDynamoDBClient.php');
	require_once('getDynamoDBItem.php');
	session_start();
	
	$client = createDynamoDBClient();
	$result = getDynamoDBItem($client, $_SESSION['email'], strtotime(date('Ymd')));
	
	if (iterator_count($result) > 0) {
		foreach ($result as $item) {
			switch ($item['Attendance']['S']) {
				case '出勤':
					$attendanceOn[] = $item['UnixTime']['N'];
					break;
				case '退勤':
					$attendanceOff[] = $item['UnixTime']['N'];
					break;
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?php echo BRAND; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron-narrow.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="bootstrap/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  <?php include_once("analyticstracking.php") ?>

    <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
          	<li class="dropdown">
          		<a href="logout.php" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $_SESSION['picture'];?>" height="32px" width="32px" class="img-circle"> ログアウト</a>
            </li>
          </ul>
        </nav>
        <h4 class="text-muted"><?php echo BRAND;?> <span class="glyphicon glyphicon-time" aria-hidden="true"></span></h4>
      </div>

      <div class="jumbotron">
        <h3><?php echo date('Y/m/d D');?><br><?php echo date('H:i');?></h3>
      </div>

      <div class="row marketing">
        <div class="col-lg-6">
          <h4>本日の出勤時刻&nbsp;
          <?php
          	if (count($attendanceOn) > 0) {
          		foreach ($attendanceOn as $item) {
          			echo date('H:i',$item);
          		}
          	} else {
          		echo '--:--';
          	}
          ?>
          </h4>
        <?php
        	if (count($attendanceOn) > 0) {
        		echo '<p class="text-center"><a class="btn btn-lg btn-primary" disabled="disabled" href="attendanceOn.php" role="button">出勤</a></p>', "\n";
        	} else {
        		echo '<p class="text-center"><a class="btn btn-lg btn-primary" href="attendanceOn.php" role="button">出勤</a></p>', "\n";
        	}
    	?>
		</div>
        <div class="col-lg-6">
          <h4>本日の退勤時刻&nbsp;
          <?php
          	if (count($attendanceOff) > 0) {
          		foreach ($attendanceOff as $item) {
          			echo date('H:i',$item);
          		}
          	} else {
          		echo '--:--';
          	}
          ?>
          </h4>
        <?php
        	if (count($attendanceOff) > 0) {
        		echo '<p class="text-center"><a class="btn btn-lg btn-default" disabled="disabled" href="attendanceOff.php" role="button">退勤</a></p>', "\n";
        	} else {
        		echo '<p class="text-center"><a class="btn btn-lg btn-default" href="attendanceOff.php" role="button">退勤</a></p>', "\n";
        	}
    	?>
        </div>
      </div>

      <footer class="footer">
      <div align="center">
        <p><a href="https://github.com/m-sakano/WorkTimeLogger">WorkTimeLogger by m-sakano</a></p>
      </div>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
