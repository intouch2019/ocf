<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";

class cls_stock_upload extends cls_renderer {
    var $params;
    var $currStore;
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
        $this->params = $params;
    }

    function extraHeaders() {
    ?>
    <script type="text/javascript">
function StockUpload() {
//    var value = $("#filename").val();
  //  alert(value);
    var form_id =  $("#form_id").val();
    var formname = eval("loadstockform");
    var params = $(formname).serialize();
//    alert("formpost/uploadProducts.php?"+params+"&form_id="+form_id);
    window.location.href = "formpost/uploadStock.php?"+params+"&form_id="+form_id;//page not created
}
            
function StockUploadCnl(){
    window.location.href = "stock/upload";
}


function fetchSampleFile(){
    //alert("here");
    window.location.href = "sample_stock_file.csv";
    
}
</script>  
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
//        print_r($formResult);
        $menuitem="stock";
        include "sidemenu.php";
//        include "sidemenu.".$this->currStore->usertype.".php";
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Upload Stock</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form  id="loadstockform" name="loadstockform" enctype="multipart/form-data" method="post" action="formpost/checkStockFile.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="loadstockform">
                                    <div class="box-body">
                                        <div class="form-group">
                                        <input type="hidden" id="filename" name="filename" value="<?php
                                        if (isset($_SESSION['stockupload_fpath'])) {
                                            echo $_SESSION['stockupload_fpath'];
                                        }
                                        ?>"/>
                                            <button type="button" class="btn btn-primary" onclick="javascript:fetchSampleFile();">Download Sample CSV File</button>                               
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputFile">File input</label>
                                            <input type="file" id="file" name="file">                                   
                                        </div>                                   
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <!--<button type="submit" class="btn btn-primary">Submit</button>-->
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'loadstockform') { 
                                     if (isset($formResult->cssClass) && $formResult->cssClass != 'success' ) {          
                                    ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                    <?php  
                                }
                                    if (isset($formResult->cssClass) && $formResult->cssClass == 'success' && !isset($_SESSION['stockupload'])) {                                                        
                                                    ?>
                                        <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>

                                         <div class="common-content-block">
                                            <button type ="button" name="stockupload_yes" id = "stockupload_yes"  class="btn btn-info" onclick="StockUpload();">YES</button>
                                            <button type ="button" name="stockupload_no" id = "stockupload_no" class="btn btn-info" onclick="StockUploadCnl();">NO</button>      
<!--                                        <input type ="button" name="productupload_yes" id = "productupload_yes" value ="YES" onclick="ProductUpload();">
                                        <input type ="button" name="productupload_no" id = "productupload_no" value ="NO" onclick="ProductUploadCnl();">  -->
                                         </div>
                                    <?php } ?>   
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>            
    <?php
    } //pageContent
}//class
?>
