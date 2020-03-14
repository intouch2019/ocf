<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_stocktransfer_create extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $grnid="";
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["grnid"]) != ""){
                $this->grnid = $this->params["grnid"];
            }
        }

	function extraHeaders() {
        ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
            div.alert *
            {
                color: red;
            }
        </style>
<script type="text/javaScript">    
 
    $(function () {
        $('#transferdate                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ').datepicker({ 
        format:'dd-mm-yyyy' 
        });
        
        $('#sinvdate').datepicker({ 
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
    
    function showdivs(totype){
        
        if(totype){
            var fromloctype = $("#fromLoctype").val();
            var toloctype = $("#toLoctype").val();
            if(fromloctype == 1 && toloctype == 2){
                dccrselected.style.display = "block";
            }else if(fromloctype == 1 && toloctype == 1){
                dcdcselected.style.display = "block";
            }else if(fromloctype == 2 && toloctype == 1){
                crdcselected.style.display = "block";
            }else if(fromloctype == 2 && toloctype == 2){
                crcrselected.style.display = "block"; 
            }
        }
    }
 

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "stocktransfer";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            $obj_location_type = LocationType::getAll();
            $obj_dc_master = $dbl->getAllDCMasters();
            $obj_cr_list = $dbl->getCRList();
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_payment_terms = $dbl->getAllPaymentTerms();
            $obj_delivery_terms = $dbl->getAllDeliveryTerms();
            $obj_transit_insurance = $dbl->getAllTransitInsurance();
            
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Stock Transfer</h2>
                        <div class="common-content-block">
                            <form  role="form" id="createstocktransfer" name="createstocktransfer" enctype="multipart/form-data" method="post" action="formpost/createstocktransfer.php">
<!--                                <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid;?>"/>-->
                                <input type = "hidden" name="form_id" id="form_id" value="createstocktransfer">
                             <div class="box box-primary"><br>
                                <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                        Select From Loc Type : <br>
                                        <select id="fromLoctype" name="fromLoctype" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" required="" onchange="">
                                            <option value="-1">Select From Loc Type</option>
                                            <?php foreach($obj_location_type as $key => $value){ ?>
                                                <option value="<?php echo $key?>"><?php echo $value;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                     <div class="col-md-6">
                                        Select To Loc Type : <br>
                                        <select id="toLoctype" name="toLoctype" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="showdivs(this.value);" required="">
                                            <option value="-1">Select To Loc Type</option>
                                            <?php foreach($obj_location_type as $key => $value){ ?>
                                                <option value="<?php echo $key?>"><?php echo $value;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                </div>
                                <div class="col-md-12" id="dccrselected" style="display:none;"> <!-- DC to CR -->
                                    <br>                                     
                                     <div class="col-md-6">
                                        Select Distribution Center : <br>
                                        <select id="dccode" name="dccode" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Distributer Center</option>
                                            <?php foreach($obj_dc_master as $dcmaster){ ?>
                                                <option value="<?php echo $dcmaster->id;?>"><?php echo $dcmaster->dc_name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                    <div class="col-md-6">
                                        Select Consignment Center : <br>
                                        <select id="crcode" name="crcode" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Consignment Center</option>
                                            <?php foreach($obj_cr_list as $crmaster){ ?>
                                                <option value="<?php echo $crmaster->id;?>"><?php echo $crmaster->dispname;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                </div> 
                                <div class="col-md-12" id="dcdcselected" style="display:none;"> <!-- DC to DC -->
                                    <br>                                     
                                     <div class="col-md-6">
                                        Select From Distribution Center : <br>
                                        <select id="dccode" name="dccode1" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select From Distributer Center</option>
                                            <?php foreach($obj_dc_master as $dcmaster){ ?>
                                                <option value="<?php echo $dcmaster->id;?>"><?php echo $dcmaster->dc_name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                    <div class="col-md-6">
                                        Select To Distributer Center : <br>
                                        <select id="crcode" name="crcode1" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select To Distributer Center</option>
                                            <?php foreach($obj_dc_master as $dcmaster){ ?>
                                                <option value="<?php echo $dcmaster->id;?>"><?php echo $dcmaster->dc_name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                </div> 
                                <div class="col-md-12" id="crdcselected" style="display:none;"> <!-- CR to DC -->
                                    <br>                                     
                                     <div class="col-md-6">
                                        Select Consignment Center : <br>
                                        <select id="dccode" name="dccode2" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Consignment Center</option>
                                            <?php foreach($obj_cr_list as $crmaster){ ?>
                                                <option value="<?php echo $crmaster->id;?>"><?php echo $crmaster->dispname;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                    <div class="col-md-6">
                                        Select Distribution Center : <br>
                                        <select id="crcode" name="crcode2" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select Distribution Center</option>
                                            <?php foreach($obj_dc_master as $dcmaster){ ?>
                                                <option value="<?php echo $dcmaster->id;?>"><?php echo $dcmaster->dc_name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                </div>
                                <div class="col-md-12" id="crcrselected" style="display:none;"> <!-- CR to CR -->
                                    <br>                                     
                                     <div class="col-md-6">
                                        Select From Consignment Center : <br>
                                        <select id="dccode" name="dccode3" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select From Consignment Center</option>
                                            <?php foreach($obj_cr_list as $crmaster){ ?>
                                                <option value="<?php echo $crmaster->id;?>"><?php echo $crmaster->dispname;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                    <div class="col-md-6">
                                        Select To Consignment Center : <br>
                                        <select id="crcode" name="crcode3" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="-1">Select To Consignment Center</option>
                                            <?php foreach($obj_cr_list as $crmaster){ ?>
                                                <option value="<?php echo $crmaster->id;?>"><?php echo $crmaster->dispname;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                </div>  
                                 <div class="col-md-12">
                                     <div class="col-md-6">
                                         <br>
                                         Select Stock Transfer Date : <br>
                                         <input type="text" id="transferdate" name="transferdate" class="form-control" placeholder="Stock Transfer Date" value="<?php echo $this->getFieldValue("transferdate"); ?>">
                                     </div>
                                 </div> 
<!--                                 <div class="col-md-12">
                                     <br>
                                     <div class="col-md-12">
                                         Insert PO Number : <br>
                                         <input type="text" id="pono" name="pono" class="form-control" placeholder="Insert PO Number" value="<?php echo $this->getFieldValue("pono"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-12">
                                         Insert Supplier Invoice Number : <br>
                                         <input type="text" id="sinvno" name="sinvno" class="form-control" placeholder="Insert Supplier Invoice Number" value="<?php echo $this->getFieldValue("sinvno"); ?>">
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                    <div class="col-md-12">
                                         Insert Supplier Invoice Date : <br>
                                         <input type="text" id="sinvdate" name="sinvdate" class="form-control" placeholder="Insert Supplier Invoice Date" value="<?php echo $this->getFieldValue("sinvdate"); ?>">
                                    </div>
                                 </div> -->
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Create Stock Transfer</button>
                                    </div>
                                 </div>
                                
                             </div>   
                            </form>
                            <?php //if ($formResult->form_id == 'createstocktransfer') { ?>
                                <div class="alert" style="display:<?php echo $formResult->showhide; ?>;"<?php echo $formResult->status; ?>>
                                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4> <?php echo $formResult->status; ?>
                                        </div>
                                <?php// } ?>
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


