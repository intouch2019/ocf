<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_products_upload extends cls_renderer{

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

    function downloadSample(){ 
        window.location.href = "productmaster.csv"; 
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
            $obj_categories = $dbl->getCategories();
            $obj_specifications = $dbl->getSpecifications();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Upload Products</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="uploadproducts" name="uploadproducts" enctype="multipart/form-data" 
                                      method="post" action="formpost/uploadproducts.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createproduct">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="file" name="csv" />
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Upload</button>
                                        </div>
                                        <div class="form-group">
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                            <input type="button" class="btn btn-primary" onclick="downloadSample();" value="Download sample csv"/>
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createproduct') { ?>
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


