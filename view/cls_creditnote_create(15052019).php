<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_creditnote_create extends cls_renderer{

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
        </style>
<script type="text/javaScript">    
 
    $(function () { 
        
        $('#grndate                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ').datepicker({ 
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
 

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "creditnote";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            
            //$obj_suppliers = $dbl->getAllActiveSuppliers();
            //$obj_payment_terms = $dbl->getAllPaymentTerms();
            //$obj_delivery_terms = $dbl->getAllDeliveryTerms();
            //$obj_transit_insurance = $dbl->getAllTransitInsurance();
            //$obj_dc_master = $dbl->getAllDCMasters();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Credit Note</h2>
                        <div class="common-content-block">
                            <form  role="form" id="createcreditnote" name="createcreditnote" enctype="multipart/form-data" method="post" action="formpost/createcreditnote.php">
<!--                                <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid;?>"/>-->
                                <input type = "hidden" name="form_id" id="form_id" value="createcreditnote">
                             <div class="box box-primary"><br>
                                 
                                 <div class="col-md-12">
                                     <br>
                                     <div class="col-md-12">
                                         <b>Insert Invoice Number </b><br>
                                         <input type="text" id="invoiceno" name="invoiceno" class="form-control" placeholder="Please insert complete Invoice No e.g- CR270001/1819-27/1" value="<?php echo $this->getFieldValue("invoiceno"); ?>">
                                     </div>
                                 </div> 

                                 <div class="col-md-12">
                                    <br>                                     
                                    <div class="col-md-12">
                                         <b>Select Credit Note Date </b> <br>
                                         <input type="text" id="grndate" name="cndate" class="form-control" placeholder="Credit Note Date" value="<?php echo $this->getFieldValue("cndate"); ?>">
                                    </div>
                                 </div>
                                 
                                 <div class="col-md-12">
                                     <br>
                                     <div class="col-md-12">
                                         <b>Insert Discount Percentage </b> <br>
                                         <input type="text" id="discount" name="discount" class="form-control" placeholder="Please insert number without % sign" value="<?php echo $this->getFieldValue("discount"); ?>">
                                     </div>
                                 </div> 
                                 
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Create Credit Note</button>
                                    </div>
                                 </div>
                                <?php if ($formResult->form_id == 'createcreditnote') { ?>
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


            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


