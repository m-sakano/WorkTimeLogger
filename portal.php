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
				case '自社出社':
					$attendanceOn[] = $item['UnixTime']['N'];
					break;
				case '自社退社':
					$attendanceOff[] = $item['UnixTime']['N'];
					break;
				case '案件先出社':
					$attendanceCustomerOn[] = $item['UnixTime']['N'];
					break;
				case '案件先退社':
					$attendanceCustomerOff[] = $item['UnixTime']['N'];
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
    <script src="bootstrap/docs/assets/js/ie-emulation-modes-warning.js"></script>

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
          <h4>案件先業務の開始時刻&nbsp;
          <?php
          	if (count($attendanceCustomerOn) > 0) {
          		foreach ($attendanceCustomerOn as $item) {
          			echo date('H:i',$item), '&nbsp;';
          		}
          	} else {
          		echo '--:--';
          	}
          ?>
          </h4>
        <?php
        	if (count($attendanceCustomerOn) > 0) {
        		echo '<p class="text-center"><a class="btn btn-lg btn-primary" disabled="disabled" href="attendanceCustomerOn.php" role="button">案件先業務開始</a></p>', "\n";
        	} else {
        		echo '<p class="text-center"><a class="btn btn-lg btn-primary" href="attendanceCustomerOn.php" role="button">案件先業務開始</a></p>', "\n";
        	}
    	?>
		</div>
        <div class="col-lg-6">
          <h4>案件先業務の終了時刻&nbsp;
          <?php
          	if (count($attendanceCustomerOff) > 0) {
          		foreach ($attendanceCustomerOff as $item) {
          			echo date('H:i',$item), '&nbsp;';
          		}
          	} else {
          		echo '--:--';
          	}
          ?>
          </h4>
        <?php
        	if (count($attendanceCustomerOff) > 0) {
        		echo '<p class="text-center"><a class="btn btn-lg btn-default" disabled="disabled" href="attendanceCustomerOff.php" role="button">案件先業務終了</a></p>', "\n";
        	} else {
        		echo '<p class="text-center"><a class="btn btn-lg btn-default" href="attendanceCustomerOff.php" role="button">案件先業務終了</a></p>', "\n";
        	}
    	?>
        </div>
      </div>

      <div class="row marketing">
        <div class="col-lg-6">
          <h4>自社勤務の開始時刻&nbsp;
          <?php
          	if (count($attendanceOn) > 0) {
          		foreach ($attendanceOn as $item) {
          			echo date('H:i',$item), '&nbsp;';
          		}
          	} else {
          		echo '--:--';
          	}
          ?>
          </h4>
        <?php
        	if (count($attendanceOn) > 0) {
        		echo '<p class="text-center"><a class="btn btn-lg btn-primary" disabled="disabled" href="attendanceOn.php" role="button">自社業務開始</a></p>', "\n";
        	} else {
        		echo '<p class="text-center"><a class="btn btn-lg btn-primary" href="attendanceOn.php" role="button">自社業務開始</a></p>', "\n";
        	}
    	?>
		</div>
        <div class="col-lg-6">
          <h4>自社勤務の終了時刻&nbsp;
          <?php
          	if (count($attendanceOff) > 0) {
          		foreach ($attendanceOff as $item) {
          			echo date('H:i',$item), '&nbsp;';
          		}
          	} else {
          		echo '--:--';
          	}
          ?>
          </h4>
        <?php
        	if (count($attendanceOff) > 0) {
        		echo '<p class="text-center"><a class="btn btn-lg btn-default" disabled="disabled" href="attendanceOff.php" role="button">自社業務終了</a></p>', "\n";
        	} else {
        		echo '<p class="text-center"><a class="btn btn-lg btn-default" href="attendanceOff.php" role="button">自社業務終了</a></p>', "\n";
        	}
    	?>
        </div>
      </div>

      <footer class="footer">
      <div align="center">
        <p><a href="https://github.com/m-sakano/WorkTimeLogger">WorkTimeLogger</a></p>
      </div>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/docs/assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
