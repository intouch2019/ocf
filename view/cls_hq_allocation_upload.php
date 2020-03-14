<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once ("lib/db/DBConn.php");

class cls_hq_allocation_upload extends cls_renderer {
    var $params;
    var $currStore;
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
        $this->params = $params;
//        print_r( $this->params);
        
    }

    function extraHeaders() {
    ?>
<script type="text/javascript">
    $(function(){
        $('#datepicker').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '+1d',
            autoclose : true,  
        });
    });
    function HQAllocationUpload() {
          //date loac select chk
    //    var value = $("#filename").val();
      //  alert(value);
        var form_id =  $("#form_id").val();
        var formname = eval("loadhqallotform");
        var params = $(formname).serialize();
//        alert("formpost/uploadHQAllocation.php?"+params+"&form_id="+form_id);
        window.location.href = "formpost/uploadHQAllocation.php?"+params+"&form_id="+form_id;//page not created
    }
            
    function HQAllocationUploadCnl(){
        window.location.href = "hq/allocation/upload";
    }


    function fetchSampleFile(){
        //alert("here");
        window.location.href = "sample_hqallocation_file.csv";
    }
</script>  
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
//        print_r($formResult);
        $menuitem="hq_allocation";
        include "sidemenu.php";
        $db = new DBConn();
//        include "sidemenu.".$this->currStore->usertype.".php";
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Upload HQ_Allocation</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form  id="loadhqallotform" name="loadhqallotform" enctype="multipart/form-data" method="post" action="formpost/checkHQAllocationFile.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="loadhqallotform">
                                    <div class="box-body">
                                        <div class="form-group">
                                        <input type="hidden" id="filename" name="filename" value="<?php
                                        if (isset($_SESSION['hqallotupload_fpath'])) {
                                            echo $_SESSION['hqallotupload_fpath'];
                                        }
                                        ?>"/>
                                        <button type="button" class="btn btn-primary" onclick="javascript:fetchSampleFile();">Download Sample CSV File</button>                               
                                        </div>
                                            <div class="form-group">
                                                <select class="form-control" id="locsel" name="locsel" >
                                                    <option value="">All Location</option>
                                                    <!--<option value="" disabled selected>Select Location</option>-->
                                                    <?php
                                                    $query = "select id,name from it_locations";
                                    //                                                    echo $query;
                                                    $lobjs = $db->fetchObjectArray($query);
                                                    if (isset($lobjs)) {
                                                        foreach ($lobjs as $lobj) {
                                                            if($lobj->id == $this->locid){ $selected="selected";}else{ $selected = "";}
                                                            ?>
                                                            <option value="<?php echo $lobj->id ?>"<?php echo $selected; ?>><?php echo $lobj->name; ?></option>
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                                </select> 
                                            </div>   
                                            <div class="form-group">
                                                <div class="input-group date" id="datepicker" >
                                                    <input type="text" value="Select Date" class="form-control" name ="allotdt" id = "allotdt">
                                                    <div class="input-group-addon" >
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </div>
                                                </div> 
                                            </div>
                                            <input type="hidden" id="allotdate" name="allotdate" value="<?php
                                                if (isset($_SESSION['allotdate'])) {
                                                    echo $_SESSION['allotdate'];
                                                }
                                                ?>"/>
                                            <input type="hidden" id="purchaselocid" name="purchaselocid" value="<?php
                                                if (isset($_SESSION['purchaselocid'])) {
                                                    echo $_SESSION['purchaselocid'];
                                                }
                                                ?>"/>
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
                                <?php if ($formResult->form_id == 'loadhqallotform') { 
                                    if (isset($formResult->cssClass) && $formResult->cssClass != 'success') {          
                                    ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                    <?php  
                                        }  
                                        if (isset($formResult->cssClass) && $formResult->cssClass == 'success' && !isset($_SESSION['hqallotupload'])) {                                                        
                                    ?>
                                        <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                        <h4> <?php echo $formResult->status; ?>        
                                            <div class="common-content-block">
                                               <button type ="button" name="hqallotupload_yes" id = "hqallotupload_yes"  class="btn btn-info" onclick="HQAllocationUpload();">YES</button>
                                               <button type ="button" name="hqallotupload_no" id = "hqallotupload_no" class="btn btn-info" onclick="HQAllocationUploadCnl();">NO</button>      
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
