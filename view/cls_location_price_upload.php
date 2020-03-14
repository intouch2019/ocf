<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once 'lib/locations/clsLocation.php';

class cls_location_price_upload extends cls_renderer {
    var $params;
    var $currStore;
    var $lid = "";
    var $appldt = "";
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
                
        $this->params = $params;        
        if ($params && isset($params['lid'])) { 
            $this->lid = $params['lid'];
        }
        
        if ($params && isset($params['appldt'])) { 
            $this->appldt = $params['appldt'];
        }
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
    var selloctype = $("#selltype").val();
    //alert(selloc);
    if(selloctype == ''){
        alert("Please Select Distribution Channel ");
        $("#appldt").val("");
    }else{
     window.location.href="ajax/fetchProdListCsv.php";
    }
                
}

function LocationPriceUpload() {
    var value = $("#filename").val();
    var form_id = $("#form_id").val();
    //var locid = $("#sellocation").val();
    var loctype = $("#selltype").val();
    var seldt = $("#appldt").val();
  //  alert(value);
    var formname = eval("locprform");
    
    var params = $(formname).serialize();
//    alert("formpost/uploadProducts.php?"+params+"&form_id="+form_id);
   if(loctype == ''){
       alert("Please select distribution channel first");
   }else if(seldt == "" || seldt == "Select Date"){
       alert("Please select date");
   }else{
    window.location.href = "formpost/uploadLocationPrice.php?"+params+"&form_id="+form_id+"&loctype="+loctype+"&seldt="+seldt;
   } 
}
            
function LocationPriceUploadCnl(){
    window.location.href = "location/price/upload";
}


function fetchSampleFile(){
    //alert("here");
    window.location.href = "location_price_sample_file.csv";
    
}
</script>  
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<!--<link rel="stylesheet" href="js/chosen/chosen.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
        print_r($formResult);
        
        $menuitem="product";
        include "sidemenu.php";
//        include "sidemenu.".$this->currStore->usertype.".php";
        $clsLocation = new clsLocation();
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Current Price Upload</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form  id="locprform" name="locprform" enctype="multipart/form-data" method="post" action="formpost/checkLocPriceFile.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="locprform">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="hidden" id="filename" name="filename" value="<?php
                                        if (isset($_SESSION['locprupload_fpath'])) {
                                            echo $_SESSION['locprupload_fpath'];
                                        }
                                        ?>"/>
                                            <!--<button type="button" class="btn btn-primary" onclick="javascript:fetchSampleFile();">Download Sample CSV File</button>-->                               
                                        </div>
                                        <div class="form-group">
                                             <select id="selltype" name="selltype" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getLocationSelect(this.value);">
                                                <?php $locations = $clsLocation->getLocationTypes();
                                                if(!empty($locations)){
                                                    ?>
                                                    <option value="">Select Distribution Channel</option>
                                                <?php
                                                    foreach($locations as $loc){ 
                                                        if(isset($loc) && !empty($loc) && $loc != null){
                                                            //if($loc->id == 4){ continue; }
                                                            if($loc->id != 1 && $loc->id != 2 && $loc->id != 3 ){ continue; }
                                                            $selected = "";
                                                            if($loc->id == $this->lid){
                                                                $selected = "selected";
                                                            }
                                                ?>
                                                <option value="<?php echo $loc->id;?>" <?php echo $selected; ?>><?php echo $loc->name;?></option>
                                                        <?php     } }
                                                }
                                ?>

                                            </select>
                                            
                                            
                                            
<!--                                            <select id="sellocation" name="sellocation" class="chzn-select" style="width:100%" single onchange="getLocationSelect(this.value);">
                                                <?php // $locations = $clsLocation->getLocations();
//                                                if(!empty($locations)){
                                                    ?>
                                                    <option value="">Select Location</option>
                                                <?php
//                                                    foreach($locations as $loc){ 
//                                                        if(isset($loc) && !empty($loc) && $loc != null){
//                                                            $selected = "";
//                                                            if($loc->id == $this->lid){
//                                                                $selected = "selected";
//                                                            }
                                                ?>
                                                <option value="<?php // echo $loc->id;?>" <?php // echo $selected; ?>><?php // echo $loc->name;?></option>
                                                        <?php //     } }
//                                                }
                                ?>

                                            </select>-->
                                        </div>  
                                        <div class="form-group">
                                             <div class="input-group date" id="datepicker" >
                                                 <input type="text"  class="form-control" name ="appldt" id = "appldt" value="<?php if(isset($this->appldt) && trim($this->appldt)!=""){ echo $this->appldt; }else{ echo  "Select Date"; }  ?>">
                                                    <div class="input-group-addon" >
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </div>
                                                </div>
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
                                <?php if ($formResult->form_id == 'locprform') {                                     
                                    if (isset($formResult->cssClass) && $formResult->cssClass != 'success'  ) {                                                                                            
                                ?>                                
                                    <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4> <?php echo $formResult->status; ?>                                     
                                    </div>
                                <?php } 
                                
                                    if (isset($formResult->cssClass) && $formResult->cssClass == 'success'  ) {     //&& !isset($_SESSION['locprupload'])                                                   
                                ?>
                                
                                    <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4> <?php echo $formResult->status; ?>                                                                            
                                             <div class="common-content-block">
                                                <button type ="button" name="locprupload_yes" id = "locprupload_yes"  class="btn btn-info" onclick="LocationPriceUpload();">YES</button>
                                                <button type ="button" name="locprupload_no" id = "locprupload_no" class="btn btn-info" onclick="LocationPriceUploadCnl();">NO</button>      
                                             </div>                                   
                                    </div>
                                <?php  } } ?>
                            </div>
                        </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
<!--<script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>        -->
    <?php
    } //pageContent
}//class
?>
