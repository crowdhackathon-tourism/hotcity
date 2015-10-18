<?php include('connect.php'); 
if(!isset($_SESSION['user'])){
	header("location:index.php");
	
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="HotCity">

    <title>HotCity</title>
	<script src="https://meet.jit.si/external_api.js"></script>
    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="bower_components/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
	
    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">
	<!-- Timeline CSS -->
    <link href="dist/css/timeline.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

	<!--Portfolio-->
	<link href="dist/css/bootstrap.min.css" rel="stylesheet">
	 <link href="dist/css/3-col-portfolio.css" rel="stylesheet">
	 <link href="css/style.css" rel="stylesheet">
	 <link href="css/hover.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
		
<body ><!--oncontextmenu = "return false"-->
	
    <div class="" id="wrapper">		
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;background:#242424">
		
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index2.php"></a>
			</div>
            <!-- /.navbar-header -->
			
            <ul class="nav navbar-top-links navbar-right" >
				<li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <?php echo $_SESSION['username']; ?>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                       <!-- <li><a href="profile.php"><i class="fa fa-user fa-fw"></i>Προφίλ χρήστη</a>
                        </li>-->
                        <!--<li><a href="password.php"><i class="fa fa-gear fa-fw"></i> Αλλαγή κωδικού Πρόσβασης</a>
                        </li>-->
						<li><a href="mymaps"><i class="fa fa-gear fa-fw"></i> Οι χάρτες μου</a>
                        </li>
                       <!-- <li class="divider"></li>-->
                        <li><a href="logout"><i class="fa fa-sign-out fa-fw"></i> Αποσύνδεση</a>
                        </li>
                    </ul>
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
			<div class="" style="background-color:#607D8B"> 
			<img src="img/hotcity.png" alt="" height="110px" style="padding:8px">
				</div>
			
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                       
                        <li>
                            <a href="index2"><i class="fa fa-home fa-fw"></i> Home</a>
                        </li>
                        <li class="active">
                            <a href="#"><i class="fa fa-map fa-fw"></i> Maps<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse in" aria-expanded="true" style="">
                                <li>
                                    <a href="createMap"> New Map</a>
                                </li>
								<li>
                                    <a href="publicMaps"> Public Maps</a>
                                </li>
                                <li>
                                    <a href="mymaps"> My Maps</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
						 <li>
                            <a href="about"><i class="fa fa-info fa-fw"></i> About</a>
                        </li>
                        <!--<li>
                            <a href="pricing">Pricing</a>
                        </li>-->
						<!--<li>
                            <a href=""><i class="fa fa-camera fa-fw"></i> Παρακολούθηση</a>
                        </li>-->
						<!--<li>
                            <a href="parakolouthisi.php"><i class="fa fa-camera fa-fw"></i> Παρακολούθηση</a>
                        </li>-->
                    </ul>
					
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>