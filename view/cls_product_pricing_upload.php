<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_product_pricing_upload extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
       
        function __construct($params=null) {
            //parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
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
        $('#uploaddate').datepicker({  
        format:'dd-mm-yyyy' 
        });
    });
    
    function downloadSample(){ 
        window.location.href = "formpost/generateProdPriceSample.php";  
    }
    
    function hideButton(btnId){
                document.getElementById(btnId).style.display = "none";
    }


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="js/chosen/chosen.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "products";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_categories = $dbl->getCategories();
            $obj_specifications = $dbl->getSpecifications();
            $obj_crs = $dbl->getCRList();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Upload Product Pricing</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="uploadproductprice" name="uploadproductprice" enctype="multipart/form-data" method="post" 
                                      action="formpost/uploadproductprice.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="uploadproductprice">
                                    <div class="box-body">
                                        <div class="form-group">
                                             <!--<select id="crsel" name="crsel" class="selectpicker form-control" data-show-subtext="true" 
                                                     data-live-search="true" onchange="">-->
                                            <select name="selcr" id="selcr" data-placeholder="Select CR" class="selectpicker form-control" multiple style="width:100%;" onchange="setSelectedCR(this.value)">
                                              <option value="-1">Select CR</option>
                                              <option value="0">All</option>
                                           <?php  
                                                  foreach($obj_crs as $cr){
                                                      $selected = ""; ?>
                                              <option value="<?php echo $cr->id?>" <?php echo $selected;?>><?php echo strtoupper($cr->dispname); ?></option>   
                                             <?php } ?>
                                            </select>
                                        </div>                                            
                                        <div class="form-group">
                                            <input type="file" name="csv" />
                                        </div>
                                        <div class="form-group">
                                            Select Upload Date : <br>
                                         <input type="text" id="uploaddate" name="uploaddate" class="form-control" placeholder="upload Date" value="<?php echo $this->getFieldValue("uploaddate"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" id="submit_button" style="display:block" onclick="hideButton(this.id);" class="btn btn-primary">Upload</button>
                                        </div>
                                        <div class="form-group">
                                        </div>
                                    </div>
                                </form><br><br>                                    
                                <div class="box-footer">
                                        <button class="btn btn-primary" onclick="downloadSample();">Download sample csv</button>                                        
                                </div>
                                
                                <?php if ($formResult->form_id == 'uploadproductprice') { ?>
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
<!--<script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>        -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


