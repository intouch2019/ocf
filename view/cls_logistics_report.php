<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";
require_once "lib/qrcode/qrlib.php";

class cls_logistics_report extends cls_renderer{

        var $currStore;
        var $userType;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $city;
        var $dtrng;
       
        function __construct($params=null) {
 //parent::__construct(array());
        $this->currStore = getCurrStore();
        
        $this->params = $params;
            if ($params && isset($params['city'])) { 
                $this->city = $params['city'];
            }
            if ($params && isset($params['dtrng'])) { 
                $this->dtrng = $params['dtrng'];
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
$(function(){
    
     $('#reservation').daterangepicker({
      locale: {
            format: 'DD/MM/YYYY'
        }
    });
    
    var url = "ajax/tb_ord_report.php?cid=<?php echo $this->cid; ?>&uid=<?php echo $this->uid; ?>&pid=<?php echo $this->pid; ?>&sid=<?php echo $this->sid; ?>&city=<?php echo $this->city; ?>&dtrng=<?php echo $this->dtrng; ?>";
//     alert(url);
  oTable = $('#tb_ord_report').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,null,null,null], 
	"sAjaxSource": url,
        "aaSorting": []
    });
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
  	  if (e.which == 13){
  		  oTable.fnFilter($(this).val(), null, false, true);
  	  }
    });
//    $("#labels").click(function () {
//      var table = $('#tb_orders').dataTable();
//      var selectedIds = table.$(".ordersel:checked", {"page": "all"});
//      var ids = [];
//      selectedIds.each(function(index,elem){
//        var checkbox_value = $(elem).val();
//          ids.push(checkbox_value);
//        });
//      //alert("formpost/generatetxtfile.php?ids="+ids.join());
//       window.location.href="formpost/generatetxtfile.php?ids="+ids.join();
//    });
//
//   $("#example-select-all").click(function () {
//      $('.ordersel').prop('checked', this.checked);
//    });
//   $('#tb_orders tbody').on('change', 'input[type="checkbox"]', function(){
//      if(!this.checked){
//         var el = $('#example-select-all').get(0);
//         // If "Select all" control is checked and has 'indeterminate' property
//         if(el && el.checked && ('indeterminate' in el)){
//            // Set visual state of "Select all" control 
//            // as 'indeterminate'
//            el.indeterminate = true;
//         }
//      }
//   });
});

function reload(){
//    alert("hi");
    var city = $("#city").val(); 
    var dtrng = $("#reservation").val();
    dtrng =  dtrng.replace(/\//g,":");
    if(city == " " || city == null || dtrng == " "){
        alert("Select City and Date");
    }else{
         window.location.href ="logistics/report/city="+city+"/dtrng="+dtrng; 
    }   
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<!--<link rel="stylesheet" href="js/chosen/chosen.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
        <?php
        }
        public function pageContent() {
            $menuitem = "logisticsreport";//pagecode
            include "sidemenu.".$this->currStore->type.".php";
            $dbl = new DBLogic();
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-12" id='main'>
            <div class="box box-primary">
              <div class="box-header">
                <h3 class="box-title">Orders</h3>
              </div>
              <div class="box-body">
                <div class="col-sm-6">                  
                  <div class="form-group">
                    <label>Select City:</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                        <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="city" name="city">
                            <option value="" disabled selected>Select City
                            </option>
                            <?php                                                   
                            $ctobjs =$dbl->getCity();
                            if (isset($ctobjs)) {
                                foreach ($ctobjs as $ctobj) {
                                    $selected = "";
                                    if($this->city == $ctobj->id){
                                        $selected = "selected";
                                    }
                                    ?>
                                    <option value="<?php echo $ctobj->id ?>" <?php echo $selected; ?>><?php echo $ctobj->title; ?></option>
                                <?php
                                }
                            }
                            ?>
                        </select>  
                     </div><!-- /.input group -->
                  </div><!-- /.form group -->
                </div>
                <div class="col-sm-6">
                   <!--Date and time range--> 
                  <div class="form-group">
                    <label>Select Date range:</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                        <input type="text" class="form-control pull-right" id="reservation" value="<?php echo "$this->dtrng"; ?>"/>
                     </div><!-- /.input group -->
                  </div><!-- /.form group -->
                </div>
              </div>               
              <div class="box-footer">
                <div class="col-md-3">
                   <button type="button" class="btn btn-primary" onclick="reload();">Reload</button>
                </div>      
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="common-content-block">
                    <table id="tb_ord_report" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>CITY</th>
                                <th>HUB</th>
                                <th>ORDER NO</th>
                                <th>ORDER DATE</th>
                                <th>DELIVERY DATE</th>
                                <th>ORDER TYPE</th>
                                <th>CUSTOMER NAME</th>
                                <th>STATUS</th>                                
                                <th>QTY</th>
                                <th>VALUE</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="7" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              
    <?php 	}
}
?>


