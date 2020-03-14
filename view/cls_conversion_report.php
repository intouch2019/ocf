<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_conversion_report extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $purdt ="";
       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['purdt'])){
                    $this->purdt = $params['purdt'];
//                    print $this->binid;
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
	
	***********************************************************************************************************/	

</script> -->
<script type="text/javaScript">    
$(function(){
    var url = "ajax/tb_conversion_report.php?purdt=<?php echo $this->purdt;?>";
//        alert(url);
        oTable = $('#tb_conversion_report').dataTable( {
            "bProcessing": true,
            "bServerSide": true,
            "aoColumns": [null,null,null,{bSortable:false}],
            "sAjaxSource": url,
            "aaSorting": []
        } );
    // search on pressing Enter key only
        $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
            if (e.which == 13){                     
                    oTable.fnFilter($(this).val(), null, false, true);
            }
        });   
    
    
    $('#datepicker').datepicker({
        format: 'dd-mm-yyyy',
    //    startDate: '+1d', 
        autoclose : true,  
    }).change(dateChanged);
    // $("#tablebox").hide();   
});
function dateChanged(ev) {   
    var dt = $("#seldate").val();
//    dt =  dt.replace(/\//g, "-");
//    alert("DT: "+dt);
//    var pur_id = $("#pursel").val();
////    alert(loc_id);
//    if(pur_id == ''){
//        alert("here");
        window.location.href="conversion/report/purdt="+dt;
//    }else{
//        window.location.href="conversion/report/purid="+pur_id+"/purdt="+dt;
//    }              
}
 function showConversionDetails(cid) {
    window.location.href="conversion/details/cid="+cid;
 }

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "conversion_report";//pagecode
            include "sidemenu.php";    
            require_once "lib/core/strutil.php";
            $formResult = $this->getFormResult();
//            print_r($formResult);
            $db = new DBConn(); 
?>
<div class="container-section">
     <div class="row">
        <div class="col-md-4">
            <div class="input-group date" id="datepicker" >
                <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php if(isset($this->purdt) && trim($this->purdt)!=""){ echo $this->purdt; }else{ echo "Select Date"; } ?>">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
        <br/><br/> 
    </div>
    showing data of current month
    <div class="row" id="tablebox">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="common-content-block"> 
                    <table id="tb_conversion_report" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>               
                                <th>Purchase Date</th>
                                <th>Conversion Date</th>
                                <th>PO Number</th>
                                <th>Packets Loss</th>
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
        <br/><br/>    
        <input type ="submit" class="btn btn-primary"  id="submit" name="submit" style="float:right" value="Submit" >
    </div> 
    </form></br></br>
</div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}


