<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_supplier_bill_entry extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $gateentryid="";
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["gateentryid"]) != ""){
                $this->gateentryid = $this->params["gateentryid"];
            }
        }

	function extraHeaders() {
        ?>
<style type="text/css" title="currentStyle">
          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
<script type="text/javaScript">    
    $(function () {  
        $('#billdate').datepicker({
             format:'dd-mm-yyyy'
         });
    });
    
    function setCtg(ctgValue){  
        if(ctgValue == -1){
            $("#addctg").show();
        }else{
            $("#addctg").hide();
        }
    }
 
    function setSpec(specValue){
        if(specValue == -1){
            $("#addspec").show();
        }else{
            $("#addctg").hide();
        }
    }
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "supplierbill";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            //print_r($formResult);
            $dbl = new DBLogic();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Supplier Bill Entry</h2>
                        <div class="common-content-block">
                             <div class="box box-primary"><br>
                            <form  role="form" id="supplierbill" name="supplierbill" enctype="multipart/form-data" method="post" action="formpost/supplierbillentry.php">
                             <input type="hidden" name="gateentryid" id="gateentryid" value="<?php echo $this->gateentryid;?>"/>
                             <input type = "hidden" name="form_id" id="form_id" value="supplierbill">
                                 <div class="col-md-12">
                                     <div class="col-md-12">
                                         <input type="text" id="pono" name="pono" class="form-control" placeholder="Enter PO No" value="<?php echo $this->getFieldValue("pono"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         <input type="text" id="billno" name="billno" class="form-control" placeholder="Enter Bill No" value="<?php echo $this->getFieldValue("billno"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         <input type="text" id="billdate" name="billdate" class="form-control" placeholder="Enter Bill Date" value="<?php echo $this->getFieldValue("billdate"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                 </div>
                            </form><br><br>                             
                                <?php if ($formResult->form_id == 'supplierbill') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                             </div> <!--class box primary-->  
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


