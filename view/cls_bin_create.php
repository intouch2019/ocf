<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_bin_create extends cls_renderer{

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
<!--<link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "bins";//pagecode
            include "sidemenu.php";  
            include 'lib/locations/clsLocation.php';
            $formResult = $this->getFormResult();
            $clsLocation = new clsLocation();
//            print_r($formResult);
//                        include "sidemenu.".$this->currStore->usertype.".php";    
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Bin</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary"><br>
                                <form role="form" id="createbin" name="createbin" enctype="multipart/form-data" method="post" action="formpost/addBin.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createbin">
                                    <div class="box-body">
                                        <div class="form-group" >
                                            <!--<div  style=" width: 46%">-->
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="locsel" name="locsel">
                                                    <!--<option value="">Select Location</option>-->
                                                    <option value="" disabled selected>Select Location</option>
                                                    <?php
//                                                    $query = "select id,name from it_locations";
//                                                    echo $query;
//                                                    $lobjs = $db->fetchObjectArray($query);
                                                    $lobjs = $clsLocation->getLocations();
                                                    if (isset($lobjs)) {
                                                        foreach ($lobjs as $lobj) {
                                                            $selected = "";
                                                            $stype = $this->getFieldValue("locsel");
//                                                            print "returns ---- $stype -----------$lobj->id";
                                                            if($stype == $lobj->id){
                                                                $selected = "selected";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $lobj->id ?>" <?php echo $selected; ?>><?php echo $lobj->name; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>   
                                                
                                            <!--</div>-->
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Name"  value="<?php echo $this->getFieldValue("name"); ?>">
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <!--<input type="submit" class="btn-primary" style="width:150px;" value="Create">-->
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createbin') { ?>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


