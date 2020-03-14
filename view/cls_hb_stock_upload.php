<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once 'lib/locations/clsLocation.php';

class cls_hb_stock_upload extends cls_renderer {
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
 $(function(){
        $('#datepicker').datepicker({
           // format: 'mm/dd/yyyy',
            format: 'dd-mm-yyyy',
            startDate: '1d',
            autoclose : true, 
        }).change(dateChanged);
            //.on('changeDate', dateChanged);
        
        
    });
    
    
function dateChanged(ev) {
   // alert("here");
    //var selloc = $("#sellocation").val();
    var selloctype = $("#selhub").val();
    //alert(selloc);
    if(selloctype == ''){
        alert("Please Select Hub First ");
        $("#appldt").val("");
    }else{
     window.location.href="ajax/fetchProdListForStockUplCsv.php";
    }
                
}    
    
    
function StockUpload() {
//    var value = $("#filename").val();
  //  alert(value);
    var form_id =  $("#form_id").val();
    var formname = eval("loadstockform");
    var params = $(formname).serialize();
    //alert("formpost/uploadHStock.php?"+params+"&form_id="+form_id);
    window.location.href = "formpost/uploadHStock.php?"+params+"&form_id="+form_id;//page not created
}
            
function StockUploadCnl(){
    window.location.href = "hb/stock/upload";
}


function fetchSampleFile(){
    //alert("here");
    window.location.href = "sample_stock_file.csv";
    
}
</script>  
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
//        print_r($formResult);
        //print_r($_SESSION);
        $menuitem="stock";
        include "sidemenu.php";
        $clsLocation = new clsLocation();  
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
                                <form  id="loadstockform" name="loadstockform" enctype="multipart/form-data" method="post" action="formpost/checkHStockFile.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="loadstockform">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <select id="selhub" name="selhub" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="loadhubwise(this.value);" >
                                                <option value="">Select Hub</option>
                                                <?php
                                                    $hubobjs = $clsLocation->getHubLocations();  // function added in clsLocation                 
                                //                    print_r($hubobjs);
                                                    if(!empty($hubobjs)){
                                                        foreach($hubobjs as $hubobj){
                                                            if(isset($hubobj) && !empty($hubobj) && $hubobj != null){
                                                                $selected="";
                                                                if($hubobj->id == $this->heid){
                                                                    $selected = "selected";
                                                                }
                                                                if(isset($_SESSION["h_sel_hub"]) && trim($_SESSION["h_sel_hub"])!=""){
                                                                    if($hubobj->id == $_SESSION["h_sel_hub"]){
                                                                       $selected = "selected"; 
                                                                    }
                                                                }
                                                    ?>
                                                <option value="<?php echo $hubobj->id; ?>" <?php echo $selected; ?> ><?php echo $hubobj->name; ?></option>
                                                    <?php
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div> 
                                        <div class="form-group">
                                             <div class="input-group date" id="datepicker" >
                                                 <input type="text"  class="form-control" name ="appldt" id = "appldt" value="<?php if(isset($_SESSION["h_sel_dt"]) && trim($_SESSION["h_sel_dt"])!=""){ echo $_SESSION["h_sel_dt"];}//if(isset($this->appldt) && trim($this->appldt)!=""){ echo $this->appldt; }else{ echo  "Select Date"; }  ?>">
                                                    <div class="input-group-addon" >
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="form-group">
                                        <input type="hidden" id="filename" name="filename" value="<?php
                                        if (isset($_SESSION['stockupload_fpath'])) {
                                            echo $_SESSION['stockupload_fpath'];
                                        }
                                        ?>"/><!--
                                            <button type="button" class="btn btn-primary" onclick="javascript:fetchSampleFile();">Download Sample CSV File</button>                               
                                        </div>-->
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
    <?php
    } //pageContent
}//class
?>
