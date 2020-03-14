
<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_imprest_report extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $cid;
    var $uid;
    var $pid;
    var $sid = -1;
    var $grnstatus = "";
    var $dtrng;

    function __construct($params = null) {
        $this->currStore = getCurrStore();
        $this->params = $params;
        $dbl = new DBLogic();
        if (isset($this->params["crid"]) != "") {
            $this->crid = $this->params["crid"];
            echo $this->crid;
        } else if ($this->currStore->usertype == UserType::RFC) {
            $objcr = $dbl->getCRDetailsByUserId($this->currStore->id);

            $this->crid = $objcr->id;
        } else {
            $this->crid = 1;
        }

        if ($params && isset($params['dtrng'])) {
            $this->dtrng = $params['dtrng'];
        }
    }

    function extraHeaders() {
        ?>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
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
        $(function(){    
             $('#reservation').daterangepicker({  
              locale: {
                    format: 'DD/MM/YYYY'    
                } 
            });

            
             reloadpage();
           
        }); 
        
        function updateCRSel(crid){ 
            var dtrng = $("#reservation").val();
            window.location.href = "imprest/report/crid="+crid+"&dtrng="+dtrng; 
        }


        function genExcelRep(){
            var crcode = <?php echo $this->crid; ?>;
            var dtrng = $("#reservation").val();
            
            if(crcode == 0){
                alert("Please select CR first");
            }else{
                window.location.href="formpost/genImprestExcel.php?crid="+crcode+"&dtrng="+dtrng;;
                
            }  
        } 


        function reloadpage(){ 
        //    alert("hiiii");
            var dtrng = $("#reservation").val();
            var url = "ajax/tb_imprestreport.php?crid="+<?php echo $this->crid; ?>+"&uploaddate="+dtrng;   
                        //var url = "ajax/tb_purchase_order.php";  
//                                        alert(url); 
                        oTable = $('#tb_imprestreporttable').dataTable( {
                        "bProcessing": true, 
                        "bServerSide": true,
                        "aoColumns": [null,null,null,null,null,null,null,null,null,{bSortable:false}], 
                        "sAjaxSource": url, 
                        "aaSorting": [], 
                        "iDisplayLength" : 50,
                        "bDestroy": true
                        } );
                        // search on pressing Enter key only
                        $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
                        if (e.which == 13){                     
                        oTable.fnFilter($(this).val(), null, false, true);
                        }
                        });
           
        }

        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
    <?php
    }

    public function pageContent() {
        $menuitem = "imprestreport";
        include "sidemenu." . $this->currStore->usertype . ".php";
        $dbl = new DBLogic();
        $obj_cr_master = $dbl->getCRList();
        ?>

        <div class="container-section">


            <div class="row">
                <div class="col-md-3">
                    <input type="text" class="form-control pull-right" id="reservation" value="<?php echo "$this->dtrng"; ?>"/>
                </div>
                <?php if ($this->currStore->usertype != UserType::RFC) { ?>
                    <div class="col-md-3">
                        <select id="dccode" name="dccode" class=" form-control" data-show-subtext="true"
                                data-live-search="true" onchange="updateCRSel(this.value);" >
                            <option value="-1">Select Consignment Retailer</option>
                            <?php
                            foreach ($obj_cr_master as $crmaster) {
                                $selected = "";
                                if ($crmaster->id == $this->crid) {
                                    $selected = "selected";
                                }
                                ?>
                                <option value="<?php echo $crmaster->id; ?>" <?php echo $selected; ?>><?php echo $crmaster->dispname; ?></option>
                    <?php } ?>
                        </select>
                    </div>
        <?php } ?>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary" onclick="reloadpage();">Generate Report</button>
                </div>


                <div class="col-md-3">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary pull-right" onclick="genExcelRep();">Export to Excel</button>
                </div>
            </div>    

            <br/>
            <div class="row">

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Imprest Report</b></h7>
                        <div class="common-content-block">                     
                            <table id="tb_imprestreporttable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                                <thead>
                                    <tr>  
                                        <th>CR Code</th>
                                        <th>Voucher No.</th>
                                        <th>Pre-Transaction Balance</th>
                                        <th>Transaction Amount</th>
                                        <th>Post Transaction Balance</th>
                                        <th>Description</th>
                                        <th>Reason</th>
                                        <th>By User</th>
                                        <th>Createtime</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8" class="dataTables_empty" style="width: 20px;">Loading data from server</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>    
           <!--    <script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script> 
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
                        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>-->
            <?php
        }

    }
    ?>


