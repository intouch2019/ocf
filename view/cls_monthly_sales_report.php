<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_monthly_sales_report extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $dcid=0;
        var $drange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $grnstatus = "";
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->usertype = $this->currStore->usertype;            
            $this->params = $params;
            if(isset($this->params["crid"]) != ""){
                $this->crid = $this->params["crid"];
            }else{
                 $this->crid = 0;
            }
        }

function extraHeaders() { ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<!--<script src="jqueryui/js/jquery.table2excel.js"></script> -->
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){            
  $('#datepicker').daterangepicker({  
      locale: {
            format: 'DD/MM/YYYY'    
        }
    });
});


function genExcelRep(){
    var month = $('#monthsel').val();
    var year = $('#yearsel').val();
    
    if(month == "" || year == ""){
        alert("Please select month and year both."); 
    }else{
        startDate = year+"-"+month+"-"+"01 00:00:00";
        endDate = year+"-"+month+"-"+"31 23:59:59";
        
        window.location.href="formpost/genMonthlySalesExcel.php?startDate="+startDate+"&endDate="+endDate; 
    }
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "monthlysalereport";
//            include "sidemenu.".$this->currStore->usertype.".php";
            include "sidemenu.php";
            $dbl = new DBLogic();
            $obj_cr_master = $dbl->getCRList();
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="monthsel" name="monthsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" >
                <option value="" selected >Select Month</option>
                <?php $statusarray = Months::getAll();
                foreach($statusarray as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="yearsel" name="yearsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" >
                <option value="" selected >Select Year</option>
                <?php $statusarray = Years::getAll();
                foreach($statusarray as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php }
                ?>
            </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <button type="button" id="export" class="btn btn-primary pull-right" onclick="genExcelRep();">Export to Excel</button>
        </div>
    </div>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
<!--  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              -->
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>



