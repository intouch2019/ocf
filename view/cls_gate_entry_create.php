<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_gate_entry_create extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $poid="";
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["poid"]) != ""){
                $this->poid = $this->params["poid"];
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
    
    function setCtg(ctgValue){  
        if(ctgValue == -1){
            $("#addctg").show();
        }else{
            $("#addctg").hide();
        }
    }
 
    function setUser(value){
        if(value == -1){
            $("#addreceiver").show();
        }else{
            $("#addreceiver").hide();
        }
    }


</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "gateentry";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_payment_terms = $dbl->getAllPaymentTerms();
            $obj_delivery_terms = $dbl->getAllDeliveryTerms();
            $obj_transit_insurance = $dbl->getAllTransitInsurance();
            
            $obj_nologinusers = $dbl->getNoLoginUsers(UserType::NoLogin);
            $obj_transporters = $dbl->getTransporters();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Gate Entry</h2>
                        <div class="common-content-block">
                            <form  role="form" id="creategateentry" name="creategateentry" enctype="multipart/form-data" method="post" action="formpost/createGateEntry.php">
                             <div class="box box-primary"><br>
                                 <div class="col-md-12">
                                     <div class="col-md-12">
                                        <select id="suppsel" name="suppsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Supplier</option>
                                            <?php foreach($obj_suppliers as $supp){ ?>
                                                <option value="<?php echo $supp->id;?>"><?php echo $supp->supplier_code." , ".$supp->company_name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                     <div class="col-md-12">
                                         <br>
                                        <select id="transsel" name="transsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Transporter</option>
                                            <?php foreach($obj_transporters as $transporter){ ?>
                                                <option value="<?php echo $transporter->id;?>"><?php echo $transporter->name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         <input type="text" id="lrno" name="lrno" class="form-control" placeholder="Enter LR No" value="<?php echo $this->getFieldValue("lrno"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         <input type="text" id="details" name="details" class="form-control" placeholder="Details (optional)" value="<?php echo $this->getFieldValue("details"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         <input type="text" id="qty" name="qty" class="form-control" placeholder="Parcel Quantity" value="<?php echo $this->getFieldValue("qty"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                        <select class="selectpicker form-control" data-show-subtext="true" data-lice-search="true" id="usersel" name="usersel" onchange="setUser(this.value);">
                                            <option value="0">Select Receiver</option>
                                            <?php foreach($obj_nologinusers as $user){ ?>
                                                <option value="<?php echo $user->id;?>"><?php echo $user->name;?></option>
                                            <?php }?>
                                            <option value="-1"><< Create New  >></option>
                                        </select> 
                                    </div>
                                </div>
                                <div class="form-group" style="display:none;" id="addreceiver">
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                    <input type="text" id="newreceiver" name="newreceiver" class="form-control" placeholder="Insert new receiver" value="<?php echo $this->getFieldValue("newreceiver"); ?>">
                                    </div>
                                    </div>
                                </div>
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                 </div>
                                <?php if ($formResult->form_id == 'creategateentry') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                             </div>   
                            </form>
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


