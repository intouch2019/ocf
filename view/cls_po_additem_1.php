<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_po_additem extends cls_renderer{

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
    
    //function 
    

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
            
            $obj_categories = $dbl->getAllActiveCategories();
            $obj_products = $dbl->getAllActiveProducts();
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_specifications = $dbl->getAllActiveSpecifications();
            $obj_po = $dbl->getPODetails($this->poid);
            //print_r($obj_po);
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">PO Details</h2>
                        <div class="common-content-block">
                            <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid;?>"/>
                             <div class="box box-primary"><br>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Supplier Code : <?php echo $obj_po->supplier_code;?></label>
                                        
                                    </div>
                                    <div class="col-md-4">
                                        <label>Payment Terms : <?php echo $obj_po->pmterm;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>PO No : <?php echo $obj_po->pono;?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Supplier Name : <?php echo $obj_po->company_name;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivery Terms : <?php echo $obj_po->dtterm;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>PO Date : <?php echo ddmmyy($obj_po->createtime);?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Buyer Code : <?php echo $obj_po->buyer_code;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>DC Code : <?php echo $obj_po->dccode;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Contract No : <?php echo $obj_po->supp_contract_no;?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Buyer Name : <?php echo $obj_po->buyer_name;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivery Name : <?php echo $obj_po->delivery_name;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Transit Insurance : <?php echo $obj_po->titerm;?></label>
                                    </div>
                                </div>
                             </div>   
                        </div>
                    </div>
                </div>
                <div  class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="title-bar">Add Items</h2>
                        <div class="common-content-block">
                             <div class="box box-primary"><br>
    <form  role="form" id="createpo" name="createpo" enctype="multipart/form-data" method="post" action="formpost/poAddItem.php">
                                 <div class="col-md-12">
                                     <br>
                                     <div class="col-md-4">
                                        <select id="ctgsel" name="ctgsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="ctgSelc(this.value)">
                                            <option value="-1">Select Category</option>
                                            <?php foreach($obj_categories as $cat){ ?>
                                                <option value="<?php echo $cat->id;?>"><?php echo $cat->name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                     <div class="col-md-4">
                                        <select id="prodsel" name="prodsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="prodSelc(this.value);">
                                            <option value="-1">Select Product</option>
                                            <?php foreach($obj_products as $prod){ ?>
                                                <option value="<?php echo $prod->id;?>"><?php echo $prod->prod;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                     <div class="col-md-4">
                                        <select id="specsel" name="specsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="specSelc(this.value);">
                                            <option value="-1">Select Specification</option>
                                            <?php foreach($obj_specifications as $spec){ ?>
                                                <option value="<?php echo $spec->id;?>"><?php echo $spec->name;?></option>
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br> 
                                    <div class="col-md-3">
                                        <input type="text" id="qty" name="qty" class="form-control" placeholder="Qty" value="<?php echo $this->getFieldValue("qty"); ?>">
                                    </div> 
                                    <div class="col-md-3">
                                        <input type="text" id="rate" name="rate" class="form-control" placeholder="Rate" value="<?php echo $this->getFieldValue("rate"); ?>">
                                    </div> 
                                    <div class="col-md-3">
                                        <input type="text" id="rate" name="value" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("value"); ?>">
                                    </div> 
                                    <div class="col-md-3">
                                        <input type="text" id="exdate" name="exdate" class="form-control" placeholder="Expected date" value="<?php echo $this->getFieldValue("exdate"); ?>">
                                    </div> 
                                 </div>
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Add Item</button>
                                    </div>
                                 </div>
                                <?php if ($formResult->form_id == 'createdc') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
        
    </form>                                 
                             </div>       
                        </div>
                    </div>
                </div>    
            </div>
        </div> 
 </div><!-- end -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


