<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_product_edit extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $prodid;
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            
            if(isset($this->params["prodid"])){ $this->prodid = $this->params["prodid"]; }
            
            //echo $this->prodid;
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
        
        <?php
        }

        public function pageContent() {
            $menuitem = "products";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $ctgid = null; $specid = null;
            $obj_product = $dbl->getProductById($this->prodid);
            if(isset($obj_product)){
                $ctgid = $obj_product->ctg_id;
                $specid = $obj_product->spec_id;
            }
            $obj_categories = $dbl->getCategories();
            $obj_specifications = $dbl->getSpecifications();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Product</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="updateproduct" name="updateproduct" enctype="multipart/form-data" method="post" action="formpost/updateproduct.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="updateproduct">
                                    <input type = "hidden" name="prodid" id="prodid" value="<?php echo $this->prodid;?>">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Category</label>
                                            <select class="form-control" id="ctgsel" name="ctgsel" onchange="setCtg(this.value);">
                                                <option value="0">Select Category</option>
                                                <?php 
                                                    foreach($obj_categories as $ctg){ 
                                                        $selected = "";
                                                    if($ctgid == $ctg->id){ $selected = "selected"; }?>
                                                    <option value="<?php echo $ctg->id;?>" <?php echo $selected;?>><?php echo $ctg->name;?></option>
                                                <?php }?>
                                                <option value="-1"><< Create New Category >></option>
                                            </select> 
                                        </div>
                                        <div class="form-group" style="display:none;" id="addctg">
                                            <input type="text" id="ctgnew" name="ctgnew" class="form-control" placeholder="Insert new catgeory" value="<?php echo $this->getFieldValue("ctgnew"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-6 control-label">Product description</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Product description" value="<?php echo $obj_product->name; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-6 control-label">Specification</label>
                                            <select class="form-control" id="specsel" name="specsel" onchange="setSpec(this.value);">
                                                <option value="0">Select Specification</option>
                                                <?php foreach($obj_specifications as $spec){ 
                                                    $selected = "";
                                                    if($specid == $spec->id){ $selected = "selected"; }
                                                    ?>
                                                    <option value="<?php echo $spec->id;?>" <?php echo $selected;?>><?php echo $spec->name;?></option>
                                                <?php }?>
                                                <option value="-1"><< Create New Specification >></option>
                                            </select> 
                                        </div>
                                        <div class="form-group" style="display:none;" id="addspec">
                                            <input type="text" id="specnew" name="specnew" class="form-control" placeholder="Insert new specification" value="<?php echo $this->getFieldValue("ctgnew"); ?>">
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
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


