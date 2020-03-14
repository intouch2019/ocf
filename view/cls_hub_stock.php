<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";
require_once "lib/pagination/Paginator.php";

class cls_hub_stock extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $hid;
        var $dt;
       
        function __construct($params=null) {
    //parent::__construct(array());
           $this->currStore = getCurrStore();

           $this->params = $params;

           if ($params && isset($params['hid'])) { 
               $this->hid = $params['hid'];
           }           
            if ($params && isset($params['dt'])) { 
               $this->dt = $params['dt'];
           }
        }

	function extraHeaders() {
        ?>
<style type="text/css" title="currentStyle">
    .table-responsive {
   width: 100%;
   height: 100%;
   margin-bottom: 15px;
   overflow-x: auto;
   overflow-y: auto;
   display: block;
   -webkit-overflow-scrolling: touch;
   -ms-overflow-style: -ms-autohiding-scrollbar;
   border: 1px solid #DDD;   
}

.center {
    text-align: center;
}

.pagination {
    display: inline-block;
}
.pagination a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
    transition: background-color .3s;
    border: 1px solid #ddd;
    margin: 0 4px;
}

div.pagination a.current
{
float: left;  
padding: 8px 16px;
text-decoration: none;
border: 1px solid #ddd;
font-weight: bold;
background-color: #52bfea;
color: #FFF;

}


.pagination a.active {
    background-color: #4CAF50;
    color: white;
}

.pagination a:hover:not(.active) {background-color: #ddd;}



          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
/*            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";*/
        </style>
<!-- <script src="js/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax-dynamic-list.js">
	/************************************************************************************************************
	(C) www.dhtmlgoodies.com, April 2006
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
	
	************************************************************************************************************/	

</script> -->
<script type="text/javaScript">      
$(function(){        
    $('#tb_hubstock').hide();
    $('#datepicker').datepicker({
        format: 'dd-mm-yyyy',
    //    startDate: '+1d',
        autoclose : true,  
    }).change(dateChanged);    
}); 
function dateChanged(ev) {   
    var dt = $("#seldate").val();
    var hub_id = $("#selhub").val();
//    alert(hub_id);
    if(hub_id == ''){
        alert("Select Hub First");
    }else{
//          alert("here2");
//          window.location.href="hub/stock/hid="+hub_id+"/dt="+dt;
//          var url = "ajax/tb_hubstock.php?hid=<?php //echo $this->hid; ?>";
        var ajaxurl = "ajax/gethubstockentry.php?hid="+hub_id;
//            alert(ajaxurl);
            $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    success: function (result) {
//                             alert(result);
                             $('#tab').append(result);
        //                    var trHTML= '';
        //                    
        //                    trHTML += '<tr id ="row'+ row +'"><td><input type= "hidden" name ="srno" id="srno" value="'+ cnt +'"" />' + cnt + '</td><td></td><td></td><td><input type= "hidden" name ="barcode1" id ="barcode1" value="'+ result.barcode +'"" />' + result.barcode + '</td><td></td><td></td><td></td><td><input type= "hidden" name ="name1" id ="name1" value="'+ result.pname +'"" />' + result.pname + '</td><td></td><td></td><td><input type= "hidden" name ="qty1" id ="qty1" value="'+ result.qty +'" />' + result.qty + '</td><td></td><td></td>&nbsp;<td><input type="button" name="rinvItem" id="rinvItem" value="Remove Item" onclick="removeItem(row'+ row +', ' + result.qty + ' )"></td></tr>';
//                                 trHTML += '<tr id ="row"><td><input type= "hidden" name ="srno" id="srno" value="'+ cnt +'"" />' + cnt + '</td><td><input type= "hidden" name ="barcode1" id ="barcode1" value="'+ result.barcode +'"" />' + result.barcode + '</td><td><input type= "hidden" name ="name1" id ="name1" value="'+ result.pname +'"" />' + result.pname + '</td><td><input type= "hidden" name ="qty1" id ="qty1" value="'+ result.qty +'" />' + result.qty + '</td></tr>';
        //                    $('#tb_body').append(trHTML);
        //                    $("#prod_barcode").focus();
        //                    $("#prod_barcode").val("");
        //                    $("#qty").attr('disabled', true);
        //                    $("#qty").val("");   
                            // return false;               
                    }
            });
                      $('#tb_hubstock').show();
    }              
}

function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("tab");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
     
        <?php
        }

        public function pageContent() {
           // print_r($_SESSION);
            //$currUser = getCurrUser();
            $menuitem = "hubstock";//pagecode
            include "sidemenu.php";  
            include 'lib/locations/clsLocation.php';
            $clsLocation = new clsLocation(); 
            $paginator = new Paginator();
            $totObj = $clsLocation->getProductCnt();
//            print_r($totObj);
            $tot_pg_cnt = ceil($totObj->cnt / $paginator->itemsPerPage);  
            $paginator->total = $tot_pg_cnt;
            
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="selhub" name="selhub" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                <option value="">All Hub</option>
                <?php
                    $hubobjs = $clsLocation->getHubLocations();
                    if(!empty($hubobjs)){
                        foreach($hubobjs as $hubobj){
                            if(isset($hubobj) && !empty($hubobj) && $hubobj != null){
                                $selected="";
                                if($hubobj->id == $this->hid){
                                    $selected = "selected";
                                }
                    ?>
                <option value="<?php echo $hubobj->id; ?>" <?php echo $selected; ?>><?php echo $hubobj->name; ?></option>
                    <?php
                            }
                        }
                    }
                ?>
            </select>
        </div>  
        <div class="col-md-4">
            <div class="input-group date" id="datepicker" >
                <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php if(isset($this->dt) && trim($this->dt)!=""){ echo $this->dt; }else{ echo "Select Date"; } ?>">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
    </div>
    
    <br/>
    <?php //if(isset($this->hid) && trim($this->hid)!=""){  class="row"?>
    <div id="tb_hubstock" >        
        <div class="col-md-12">    
            <div class="panel panel-default">
<!--                <div class="pagination">
                            <?php
//                                $paginator->paginate();
//                                echo $paginator->pageNumbers();
//                               echo $paginator->itemsPerPage();
                            ?>
                        </div>-->
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Hub Stock </b></h7>
                <?php 
//                    $start_from = ($paginator->currentPage-1)*$paginator->itemsPerPage;      
//                    $limit_till = $paginator->itemsPerPage; 
                ?>
<!--                <div class="table-area">-->
                <!--<div class=" table table-responsive">-->   
                    <!--<div class="table-responsive table-fixedheader">-->  
                    <p class="pull-right">
                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for products..">
                        </p>
                        <table class="table table-responsive" id="tab">
                            <!--table created dynamically-->
                        </table>
                        
                    <!--</div>-->
                <!--</div>-->
<!--                <div class="pagination">
                    <?php
                        //$paginator->paginate();
                        //echo $paginator->pageNumbers();
                      // echo $paginator->itemsPerPage();
                    ?>
                </div>-->
            </div>
        </div>
    </div>
    <?php  //} ?>
</div>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


