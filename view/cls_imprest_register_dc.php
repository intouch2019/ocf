<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_imprest_register_dc extends cls_renderer {

    var $currStore;
    var $userid;
    var $params;
    var $ledgerid;

    function __construct($params = null) {
    
        $this->currStore = getCurrStore();
       
        $this->userid = getCurrStoreId();
        
        if ($params && isset($params['reason'])) {
            $this->reason = $params['reason'];
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
                var url = "ajax/tb_imprest_details_dc.php";   
//                alert(url);
//                return;   
                //var url = "ajax/tb_purchase_order.php";  
//                        alert(url); 
                oTable = $('#tb_sales').dataTable( {
                "bProcessing": true, 
                "bServerSide": true,  
                "aoColumns": [null,null,null,null,null,null,null,{bSortable:false}], 
                "sAjaxSource": url, 
                "aaSorting": []        
                } );
                // search on pressing Enter key only
                $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
                if (e.which == 13){                     
                oTable.fnFilter($(this).val(), null, false, true);
                }
                });       
   
            });
                   
            
            
            function showPDF(impDetailsId){
//                alert(impDetailsId);
                var myWindow = window.open('',"_blank");
                myWindow.location.href = 'ajax/printImprestVoucher.php?impDetailsId='+impDetailsId;                       
                myWindow.focus();     
            }
            
            function hideButton(btnId){
                document.getElementById(btnId).style.display = "none";
            }

        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
        <?php
    }

    public function pageContent() {
        $menuitem = "imprest_register";
        include "sidemenu." . $this->currStore->usertype . ".php";
        $dbl = new DBLogic();
        $currStore = getCurrStore();  
       
        $dcid = $currStore->dcid;
        $prevImpObj = $dbl->getImprestDetailsByDcId($dcid);
        
        if($prevImpObj != null){
            $prevBal = $prevImpObj->balance;
        }else{
            $prevBal = 0;
        }
        $formResult = $this->getFormResult();
        
        $array = ImprestReason::getAll();
        $obj_imp_ledger = $dbl->getImprestledger();
//        print_r($obj_imp_ledger);
//        return;
        ?>

        <div class="container-section">

            <div class="row">
                <div class="col-md-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h1 class="title-bar">Imprest Register</h1>
                            <div class="common-content-block">
                                <form role="form" id="imprest" name="imprest" enctype="multipart/form-data" method="post" action="formpost/addImprest_dc.php">
                                    <div class="col-sm-3">
                                        <label>Reason :</label>
                                        <select id="impreason" name="impreason" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                            <option value=0 disabled="" selected="">Select Reason</option>
                                            <?php foreach($array as $key => $value){ 
                                                $selected = "";
                                                if($key == $this->status){ $selected = "selected"; }?>
                                                <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Amount : Available( <?php echo $prevBal ?> )</label>
                                            <input type="number" id="amount" name="amount"  class="form-control" placeholder="Amount" type="numbrer" value="" required=""/>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label>Ledger :</label>   
                                        <select id="impledger" name="impledger" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                            <option value=0 disabled="" selected="">Select Ledger</option>
                                            <?php foreach($obj_imp_ledger as $impmaster){
                                                          $selected = "";
                                                     
                                                      if($impmaster->id == $this->dcid){ $selected = "selected"; }?>
                                               <option value="<?php echo $impmaster->id;?>" <?php echo $selected;?>><?php echo $impmaster->ledger;?></option>
                                                      <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>Description :</label>
                                            <input type="text" id="description" name="description"  class="form-control" placeholder="Description" value="" required=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <br>
                                            <button type="submit" class="btn btn-primary" id="add_item_button" style="display:block; float: right;" onclick="hideButton(this.id);" >Submit</button>    
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="alert alert-<?php echo $formResult->cssClass; ?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;"<?php echo $formResult->status; ?>>
                                                <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                                <h4> <?php echo $formResult->status; ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Imprest List</b></h7>
                        <div class="common-content-block">                     
                            <table id="tb_sales" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                                <thead>
                                    <tr>  
                                        <th>Voucher No.</th>
                                        <th>Prev Amount</th>
                                        <th>Amount</th>
                                        <th>Current Amount</th>
                                        <th>Ledger Name</th>
                                        <th>Description</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8" class="dataTables_empty" ><center>Loading data from server</center></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                   <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
                   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
                   <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              -->
                   <!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
                   <script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php
            // }else{ print "You are not authorized to access this page";}
        }

    }
    ?>


