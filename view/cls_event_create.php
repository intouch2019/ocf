<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_event_create extends cls_renderer{

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
    
$(function(){
    $('#dweek').datepicker({
        format: "mm/dd/yyyy",
                weekStart: 1,
                autoclose: true,
                todayHighlight: true,
    });
//   $('#dweek').datetimepicker({
//        format: "dddd: HH:mm"
//    }); 
//     $('#datepicker').datepicker({
//        format: 'dd-mm-yyyy',
//    //    startDate: '+1d',
//        autoclose : true,  
////        onChange: function() {        
////        }
//    });
});


//$(function(){
//    $('#dweek').datepicker({
//        format: 'dd-mm-yyyy',
//    //    startDate: '+1d',
//        autoclose : true,  
////        onChange: function() {        
////        }
//    });
//       
//});
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "event";//pagecode
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
                        <h2 class="title-bar">Create Event</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary"><br>
                                <form role="form" id="createevent" name="createevent"  method="post" action="formpost/addEvent.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createevent">
                                    <div class="box-body">                                      
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" placeholder="Name">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="dweek" name="dweek" placeholder="Day Of Week">
                                        </div>                                       
                                    </div>
                                    <div class="box-footer">
                                        <!--<input type="submit" class="btn-primary" style="width:150px;" value="Create">-->
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createevent') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php  } ?>
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


