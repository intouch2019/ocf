<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_po_create extends cls_renderer{

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
            $menuitem = "po";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_payment_terms = $dbl->getAllPaymentTerms();
            $obj_delivery_terms = $dbl->getAllDeliveryTerms();
            $obj_transit_insurance = $dbl->getAllTransitInsurance();
            $obj_dc_master = $dbl->getAllDCMasters();
            $obj_uom = $dbl->getAllUOM();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create PO</h2>
                        <div class="common-content-block">
                            <form  role="form" id="createpo" name="createpo" enctype="multipart/form-data" method="post" action="formpost/createpo.php">
                                <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid;?>"/>
                                <input type = "hidden" name="form_id" id="form_id" value="createrpo">
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
<!--                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         <input type="text" id="suppcontractno" name="suppcontractno" class="form-control" placeholder="Enter supplier contract no" value="<?php echo $this->getFieldValue("suppcontractno"); ?>">
                                     </div>
                                 </div> -->
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                        <select id="paymentterms" name="paymentterms" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Payment Terms</option>
                                            <?php foreach($obj_payment_terms as $payment){ ?>
                                                <option value="<?php echo $payment->id;?>"><?php echo $payment->code." , ".$payment->term;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                        <select id="deliveryterms" name="deliveryterms" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Delivery Terms</option>
                                            <?php foreach($obj_delivery_terms as $delivery){ ?>
                                                <option value="<?php echo $delivery->id;?>"><?php echo $delivery->code." , ".$delivery->term;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                        <select id="transitinsurance" name="transitinsurance" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Transit Insurance</option>
                                            <?php foreach($obj_transit_insurance as $transit){ ?>
                                                <option value="<?php echo $transit->id;?>"><?php echo $transit->code." , ".$transit->term;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
<!--                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                         <input type="text" id="buyercode" name="buyercode" class="form-control" placeholder="Buyer code" value="<?php echo $this->getFieldValue("buyercode"); ?>">
                                     </div>
                                     <div class="col-md-6">
                                         <input type="text" id="buyername" name="buyername" class="form-control" placeholder="Buyer name" value="<?php echo $this->getFieldValue("buyername"); ?>">
                                     </div>
                                 </div> -->
<!--                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                         <input type="text" id="referance1" name="referance1" class="form-control" placeholder="Referance 1 (optional)" value="<?php echo $this->getFieldValue("referance1"); ?>">
                                     </div>
                                     <div class="col-md-6">
                                         <input type="text" id="referance2" name="referance2" class="form-control" placeholder="Referance 2 (optional)" value="<?php echo $this->getFieldValue("referance2"); ?>">
                                     </div>
                                 </div> -->
<!--                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                         <input type="text" id="dccode" name="dccode" class="form-control" placeholder="DC Code" value="<?php echo $this->getFieldValue("dccode"); ?>">
                                     </div>
                                     <div class="col-md-6">
                                         <input type="text" id="deliveryname" name="deliveryname" class="form-control" placeholder="Delivery Name" value="<?php echo $this->getFieldValue("deliveryname"); ?>">
                                     </div>
                                 </div> -->
                                <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                        <select id="dccode" name="dccode" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Distributer Center</option>
                                            <?php foreach($obj_dc_master as $dcmaster){ ?>
                                                <option value="<?php echo $dcmaster->id;?>"><?php echo $dcmaster->dc_name." , ".$dcmaster->address;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                </div> 
                                <div class="col-md-12">
                                    <br>                                     
                                    <div class="col-md-12">
                                         Select Unit Of Measurement : <br>
                                          <select id="uom" name="uom" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select UOM</option>
                                            <?php foreach($obj_uom as $uom){ ?>
                                                <option selected="selected" value="<?php echo $uom->id; ?>"><?php echo $uom->name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Create PO</button>
                                    </div>
                                 </div>
                                <?php if ($formResult->form_id == 'createpo') { ?>
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

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


