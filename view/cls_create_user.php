<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_create_user extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
        }

	function extraHeaders() {
        ?>
<style type="text/css" title="currentStyle">
          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
<!-- <script src="js/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax-dynamic-list.js">
	/************************************************************************************************************
	(C) www.dhtmlgoodies.com, April 2006
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
	
	************************************************************************************************************/	

</script> -->
<script type="text/javaScript">    

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "user";//pagecode
            include "sidemenu.php";   
            $formResult = $this->getFormResult();
          //  print_r($formResult);
//                        include "sidemenu.".$this->currStore->usertype.".php";    
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create User</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="createuser" name="createuser" enctype="multipart/form-data" method="post" action="formpost/create_user.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createuser">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="<?php echo $this->getFieldValue("name"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Address"  value="<?php echo $this->getFieldValue("address"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone"  value="<?php echo $this->getFieldValue("phone"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="username" name="username" class="form-control" placeholder="User name"  value="<?php echo $this->getFieldValue("username"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <!--<input type="submit" class="btn-primary" style="width:150px;height: 30px;" value="Create">-->
                                        <!--<div class="col-xs-4 col-md-2 col-md-push-3">-->
                                        <button type="submit" class="btn btn-primary">Create</button>
                                        <!--</div>-->
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createuser') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                             </div>   
                            
                        </div>
                    </div>
                </div>
            </div>
        </div> 
 </div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


