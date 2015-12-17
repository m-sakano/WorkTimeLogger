<?php
require_once('config.php');
require_once('createDynamoDBClient.php');
require_once('getDynamoDBItem.php');
require_once('getCredential.php');
require_once('createScript.php');
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

$credentials = getCredential($client, $_SESSION['email']);
if (iterator_count($credentials) > 0) {
	foreach ($credentials as $c) {
		$credential = openssl_decrypt($c['Credential']['S'], OpenSSL_ENCRYPT_METHOD, OpenSSL_ENCRYPT_KEY);
		$credentialUpdate = date('Y/m/d H:i:s', $c['UnixTime']['N']);
		break;
	}
} else {
	$credential = '';
	$credentialUpdate = 'なし';
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
            <li>
              <button type="button" class="btn btn-lg btn-link" data-toggle="modal" data-target="#myModal">
		        設定 <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
			  </button>
	  		</li>
          	<li>
          		<a class="btn btn-lg btn-link" href="logout" role="button" aria-haspopup="true" aria-expanded="false">
          		ログアウト
          		<img src="<?php echo $_SESSION['picture'];?>" height="24px" width="24px" class="img-circle">
          		</a>
            </li>
          </ul>
        </nav>
        <h4 class="text-muted"><?php echo BRAND;?> <span class="glyphicon glyphicon-time" aria-hidden="true"></span></h4>
      </div>

		<!-- Modal -->
		<form action="downloadScripts" method="POST">
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">PC連動設定</h4>
		      </div>
		      <div class="modal-body">
		        <p>
		          <label for="credential">スクリプト認証キー（最終更新：<?php echo $credentialUpdate;?>）</label>
		          <div class="input-group">
		          <input class="form-control" type="text" id="credentialview" name="credentialview" value="<?php echo $credential;?>" disabled>
		          <input class="form-control" type="hidden" id="credential" name="credential" value="<?php echo $credential;?>">
		          <span class="input-group-btn">
		          <a href="setCredential" class="btn btn-primary">認証キーを更新 <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></a>
		          </span>
		          </div>
		        </p>
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="headingOne">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
				          案件先 - ログオンスクリプト(.vbs)
				        </a>
				      </h4>
				    </div>
				    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
				      <div class="panel-body">
				      	<?php echo str_replace("\n","<br>",createScript($_SESSION['email'], $credential, '3')); ?>
				      </div>
				    </div>
				  </div>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="headingTwo">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
				          案件先 - ログオフスクリプト(.vbs)
				        </a>
				      </h4>
				    </div>
				    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
				      <div class="panel-body">
				      	<?php echo str_replace("\n","<br>",createScript($_SESSION['email'], $credential, '4')); ?>
				      </div>
				    </div>
				  </div>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="headingThree">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
				          自社勤務 - ログオンスクリプト(.vbs)
				        </a>
				      </h4>
				    </div>
				    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
				      <div class="panel-body">
				      	<?php echo str_replace("\n","<br>",createScript($_SESSION['email'], $credential, '1')); ?>
				      </div>
				    </div>
				  </div>
				  <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="headingFour">
				      <h4 class="panel-title">
				        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
				          自社勤務 - ログオフスクリプト(.vbs)
				        </a>
				      </h4>
				    </div>
				    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
				      <div class="panel-body">
				      	<?php echo str_replace("\n","<br>",createScript($_SESSION['email'], $credential, '2')); ?>
				      </div>
				    </div>
				  </div>
				</div>
		        <div class="alert alert-info" role="alert">
		        	認証キーを更新してスクリプトをコピーし、メモ帳にペーストして拡張子(.vbs)で保存します。<br>
		        	スクリプトをダブルクリックしてWorkTimeLoggerに記録されることを確認して、ログオン・ログオフスクリプトに登録します。<br>
		        	記録したテストデータはWorkTimeEditorで削除します。
		        </div>
		        <div class="alert alert-warning" role="alert">
		        	Windows Server 2012 R2, Windows 8, Windows RT 8.1 またはそれ以降の環境でグループポリシーの
		        	[ログオン スクリプトの遅延を構成する]が未構成の場合、スクリプトの実行が5分遅延します。<br>
		        </div>
		        <div class="alert alert-warning" role="alert">
		        	スクリプトが第三者に悪用された場合、想定外の時刻に打刻されることがあります。<br>
		        	案件先が変わる場合などは、認証キーを更新します。
		        </div>
		        <div class="alert alert-warning" role="alert">
		        	ログオン・ログオフスクリプトの実行が制限されている環境では
		        	PCやスマートフォンのブラウザから手動で実行します。
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
		      </div>
		    </div>
		  </div>
		</div>
		</form>
		
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
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="jquery/jquery.min.js"></script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
  </body>
</html>
