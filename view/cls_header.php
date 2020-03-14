<?php
require_once "session_check.php"; 

class cls_header {

	public function pageHeader($renderObj) {
		$currStore = getCurrStore();
		$store_name="";
		if ($currStore) { $store_name="[$currStore->username] $currStore->name"; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<base href="<?php echo $renderObj->baseUrl(); ?>"></base>
<meta content="index,follow" name="robots" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $renderObj->pageTitle(); ?></title>
<meta content="<?php echo $renderObj->pageKeywords(); ?>" name="keywords" />
<meta content="<?php echo $renderObj->pageDescription(); ?>" name="description" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<link rel="icon" href="favicon.ico" type="image/x-icon" />

<script src="jqueryui/js/jquery-1.5.1.min.js"></script>
<script src="jqueryui/js/jquery-ui-1.8.14.custom.min.js"></script>
<link rel="stylesheet" href="jqueryui/css/custom-theme/jquery-ui-1.8.14.custom.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


    <link href="css/bootstrap.css" rel="stylesheet" media="all">
    <!-- <link href="css/font-awesome.min.css" rel="stylesheet" media="all"> -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" media="all">

<!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <!-- <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css"> -->
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
   <!-- daterange picker -->
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <!-- <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"> -->
 
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
      
  <link rel="stylesheet" href="bower_components/select2/dist/css/select2.min.css">    

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   
<script src="js/common.js"></script>
<?php echo $renderObj->extraHeaders(); ?>

<style type="text/css">
  .main-header {
      max-height: 100px;
      position: fixed;
      z-index: 1030;
  }
  #page-content-wrapper {
    padding-left: 230px;
  }
  .main-sidebar {
    padding-top: 100px;
  }

  @media (max-width: 767px) {
    #page-content-wrapper {
      padding-left: 0px;
    }
  }

  .main-header .navbar-right {
    float: right;
  }

</style>

</head>
<body class="hold-transition skin-blue sidebar-mini">


 <div class="main">
        <!-- Main body content start here -->
        <div id="wrapper">
            
            <!-- Header section -->
            <header class="main-header">
                <!-- Toggle menu for mobile -->
                <!-- <a href="#menu-toggle" id="menu-toggle" class="navbar-toggle">
                    <span class="icon-bar"></span> 
                    <span class="icon-bar"></span> 
                    <span class="icon-bar"></span> 
                </a> -->
                <a href="#" id="menu-toggle" class="navbar-toggle" data-toggle="push-menu">
                    <span class="icon-bar"></span> 
                    <span class="icon-bar"></span> 
                    <span class="icon-bar"></span> 
                </a>
          <!--        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a> -->

                <!-- Logo -->
                <div class="logo">
                    <a href="javascript:void(0);">
                        <img src="images/logo.png" alt="Intouch Partner Network">
                    </a>
                </div>

                <!-- Notifications and Profile picture -->
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <li class="notifications-label hidden-xs">
                        <?php if (getCurrStore()) { 
                             echo " ".$currStore->username; ?> <span>|</span>  <?php } ?> 
                        </li>
                         <?php if (getCurrStore()) {  ?>
                        <li class="notifications-wc">
                           
                            Welcome <span><?php  echo $currStore->name; ?></span>
                           
                        </li>
<!--                        <li class="notifications-menu">
                            <a href="javascript:;">
                                <i class="fa fa-bell-o" aria-hidden="true"></i>
                                <span class="label label-danger"></span>
                            </a>
                        </li>-->
                        <li class="user-menu">
                            <a href="logout.php">
                                <img src="images/profile-pic.jpg" class="img-circle" alt="User Image">
                                <i class="online"></i>
                            </a>
                        </li>
                         <?php } ?>
                    </ul>
                </div>
            </header>
            <?php } }
