<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_deposit_details extends cls_renderer {

    var $currStore;
    var $userid;
    var $params;

    function __construct($params = null) {
        $this->currStore = getCurrStore();
        $this->params = $params;
//        print_r($params);
        if (isset($params['paymentType'])) {
            $this->paymentType = $params['paymentType'];
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
                var url = "ajax/tb_deposite_details.php";
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
            function reload(){  
            var paymentType = $("#paymentType").val(); 
            if(paymentType == " " || paymentType == null){
            alert("Select Transaction Type"); 
            }else{          
            window.location.href ="deposit/details/paymentType="+paymentType; 
            } 
            }

        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
        <?php
    }

    public function pageContent() {
        $menuitem = "deposit_details";
        include "sidemenu." . $this->currStore->usertype . ".php";
        $dbl = new DBLogic();
        $formResult = $this->getFormResult();
        $collectionRegObj = $dbl->checkOpenSaleStatus($userid);
        ?>

        <?php
        if ($collectionRegObj->id != 0) {
            echo '<script type="text/javascript">',
            'hideOpenBtn();',
            'showCloseBtn();',
            '</script>'
            ;
            ?>


            <div class="container-section">

                <div class="row">
                    <div class="col-md-12">
                        <div  class="panel panel-default">
                            <div class="panel-body">
                                <h1 class="title-bar">Deposit Diary</h1>
                                <div class="common-content-block">
                                    <form role="form" id="imprest" name="imprest" enctype="multipart/form-data" method="post" action="formpost/addDepositDetails.php">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Select Transaction Type :</label>
                                                    <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="paymentType" name="paymentType" onchange="reload()">
                                                        <option value="" disabled selected>Select Transaction Type
                                                        </option>
                                                        <?php
                                                        $rsnobjs = $dbl->getPaymentType();
                                                        $rsnobj = $rsnobjs[0];
                                                        $new_Str = str_replace("charges", "", $rsnobj->chargetypedesc);
//                                                if (isset($rsnobjs)) {
//                                                    
//                                                    foreach ($rsnobjs as $rsnobj) {
//                                                        $selected = "";
//                                                        if ($this->paymentType == $rsnobj->id) {
                                                        $selected = "selected";
//                                                        }
                                                        ?>
                                                        <option value="<?php echo $rsnobj->id ?>" <?php echo $selected; ?>><?php echo $new_Str; ?></option>
                                                        <?php
//                                                    }
//                                                }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Insert Receipt No :</label>
                                                    <input type="text" id="receiptno" name="receiptno"  class="form-control" placeholder="Receipt No"  value=""/>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Amount :</label>
                                                    <input type="number" id="amount" name="amount"  class="form-control" placeholder="Amount" type="numbrer" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Description :</label>
                                                    <input type="text" id="description" name="description"  class="form-control" placeholder="Description" value=""/>
                                                </div>
                                            </div>


                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <br>
                                                    <button type="submit" style="float: right; " class="btn btn-primary">Submit</button>    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-<?php echo $formResult->cssClass; ?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;"<?php echo $formResult->status; ?>>
                                                <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                                <h4> <?php echo $formResult->status; ?>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } else { ?>

                <div class="row">
                    <div class="col-md-12">
                        <div  class="panel panel-default">
                            <div class="panel-body">
                                <h1 class="title-bar"> <a href="/sales/create">Click Here</a> To Day Open</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
                
                <br/>
            <div class="row">

                <div class="col-md-12">

                    <div class="panel panel-default">
                        <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Deposite Details</b></h7>
                        <div class="common-content-block">                     
                            <table id="tb_sales" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                                <thead>
                                    <tr>  
                                        <th>ID</th>
                                        <th>CR Code</th>
                                        <th>Receipt Number</th>                                                             
                                        <th>Amount</th>
                                        <th>Transaction Type</th>
                                        <th>Description</th>
                                        <th>By User</th>                                                             
                                        <th>Date</th>
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


