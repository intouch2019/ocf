<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_creditnote_additem extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $poid = "";

    function __construct($params = null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["cnid"]) != "") {
            $this->cnid = $this->params["cnid"];
        }
    }

    function extraHeaders() {
        ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" /> 
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
    @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
    @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
    div.alert *
    {
        color: red;
    }
</style>
<script type="text/javaScript">   

  $(function () {      
                $('#saledate').datepicker({     
                     format:'dd-mm-yyyy'     
                 });
                 
                $('#prodsel').change(function() { 
                    $("#numberofunits").val(""); 
                    $("#rate").val(""); 
                    $("#value").val(""); 
                    $("#qty").val(""); 
                      
                }); 
               
 });
            
            function selectBatchCode(stockcurrid){
                var arr = stockcurrid.split("::");
                var itemid = arr[0];
                var prodid = arr[1]; 
                //var ajaxURL = "ajax/getCRBatchcodeByProduct.php?prodid=" + prodid;  
                var ajaxURL = "ajax/getInvBatchcodeByProduct.php?prodid=" + prodid +"&itemid="+itemid; 
                $.ajax({
                url:ajaxURL,
                    //dataType: 'json',
                    cache: false, 
                    success:function(html){
                        alert(html);
                            $('#batchcodes').html(html); 
                            $('#batchcodes').selectpicker('refresh');
                    }
                });
         
            }
            
            function getItemDetails(stockcurrid){ 
                var arr = stockcurrid.split("::");
                //var saledate = $("#saledate").val();
                var itemid = arr[0];
                var prodid = arr[1];
                //var ajaxURL = "ajax/getProductRate.php?prodid=" + prodid +"&saledate="+saledate;
                var ajaxURL = "ajax/getItemDetails.php?prodid=" + prodid +"&itemid="+itemid;
                    //alert(ajaxURL);
                    $.ajax({ 
                    url:ajaxURL,
                        dataType: 'json',
                        success:function(data){
                            //alert(data.error);
                            if (data.error == "1") {
                                alert(data.msg);
                            } else {
                                $("#invbaserate").val(data.actualrate);
                                $("#invrate").val(data.mrp);
                                $("#invvalue").val(data.totalvalue);
                                $("#batchcode").val(data.batchcode);
                                $("#qty").val(data.qty);
                                $("#invqty").val(data.qty);
                                $("#kgperpc").val(data.kgperpc);
                                var invBaseRate = $("#invbaserate").val();
                                var todaysbaserate = data.todaysprice;
                                var discount = parseFloat($("#discount").val());
                                var base_rate = 0;
                                if(invBaseRate < todaysbaserate){
                                    base_rate = invBaseRate;
                                }else{
                                    base_rate = todaysbaserate;
                                }
                                $("#baserate").val(base_rate);
                                disc = base_rate * (discount / 100);
//                                alert(disc);
                                base_rate = roundToTwo(base_rate - disc).toFixed(2);
                                $("#discrate").val(base_rate);
                                var trate = (18 / 100);
                                var taxable_amt = roundToTwo(base_rate / trate);
                               // alert("taxamt "+taxable_amt);
                                //var tax_amt = roundToTwo(base_rate - taxable_amt);
                                var tax_amt = roundToTwo(base_rate * trate).toFixed(2);
                                //alert("tax "+tax_amt);
                                //alert(tax_amt / 2);
                                var cgst_amt = roundToTwo(tax_amt / 2).toFixed(2);
                                var sgst_amt = roundToTwo(tax_amt / 2).toFixed(2);
                                var mrp = roundToTwo(parseFloat(base_rate) +  parseFloat(cgst_amt) + parseFloat(sgst_amt)).toFixed(2);
                                //alert("mrp "+mrp);
                                //$("#rate").val(data.msg);
                                
                                $("#rate").val(mrp);
                                
                                var kgperpc = $("#kgperpc").val();
                                var qt = data.qty;
                                var nou = qt / kgperpc;
                                nou = nou.toFixed(2);
                                var value = roundToTwo(qt * mrp).toFixed(2);
                                //alert(nou);
                                $("#value").val(value);
                                $("#numberofunits").val(nou);
                            }
                        }
                    });
            }
             
            function checkqty(value){
                var enterqty = parseFloat($("#qty").val());
                var invqty = parseFloat($("#invqty").val());
                if(enterqty > invqty){
                    alert("Please enter qty less than invoice quantity");
                    //$("#pieces").val("");
                    $("#qty").val("");
                    return false;
                }else{ 
                    var qt = $("#qty").val();
                    var rate = $("#rate").val();
                    var value = roundToTwo(qt * rate).toFixed(2); 
                    $("#value").val(value);
                }
            }
            
            function cancelSales(salesid){
                //alert(salesid);
                var isCancel = confirm("Are you sure to cancel this transction");
                if(isCancel){
                    if(salesid == 0){
                         window.location.href = "sales/status="+0;
                    }else if(salesid > 0){
                    var ajaxURL = "ajax/cancelSales.php?salesid=" + salesid;
                    //alert(ajaxURL);
                    $.ajax({ 
                        url:ajaxURL,
                        dataType: 'json',
                        success:function(data){
                            //alert(data.error);
                            if (data.error == "1") {
                                alert(data.msg);
                            } else {
                                //alert("Transction Successfully cancelled.");
                                window.location.href = "sales/status="+0;
                            }
                        }
                    });
                }
                 
                }
                
            }
        
            
            function deleteCreditNoteItem(itemid){  
                var cnid = $("#cnid").val();
                 //var custid = $("#custid").val();
                 var isDelete = confirm("Are you sure to delete this item?");
                 //alert(isDelete);
                 if(isDelete){
                    if(itemid > 0){
                        var ajaxURL = "ajax/deleteCreditNoteItem.php?itemid=" + itemid;
                        $.ajax({  
                            url:ajaxURL,
                            dataType: 'json',
                            success:function(data){
                                if (data.error == "1") {
                                    alert(data.msg);
                                } else {
                                    window.location.href = "creditnote/additem/cnid="+cnid;
                                }
                            }
                        });
                      }
                }
            }
             
           
            
    function roundToThree(num) {    
      return +(Math.round(num + "e+3")  + "e-3");  
    }
    
    function roundToTwo(num) {    
      return +(Math.round(num + "e+2")  + "e-2");  
    }

        </script>
        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
    }

    public function pageContent() {
        $menuitem = "creditnote"; //pagecode
        include "sidemenu." . $this->currStore->usertype . ".php";
        $formResult = $this->getFormResult();
        $dbl = new DBLogic();
        $obj_cn = $dbl->getCNDetails($this->cnid);
        //print_r($obj_cn);
        $obj_cnitems = $dbl->getCNItems($this->cnid);
        $obj_invitems = null;
        if($obj_cn != null){
            $obj_invitems = $dbl->getInvoiceItems($obj_cn->invoiceid,$this->currStore->id);
            //print_r($obj_invitems);
        }

        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Credit Note Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="cnid" id="cnid" value="<?php echo $this->cnid; ?>"/>
                                <div class="box box-primary"><br>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>Credit Note No : <?php echo $obj_cn->cnno; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Credit Note Date : <?php echo $obj_cn->cndate; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Invoice No : <?php echo $obj_cn->invoice_no; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Invoice Date : <?php echo $obj_cn->invoice_date; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>Customer Name: <?php echo $obj_cn->cname; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Customer Phone : <?php echo $obj_cn->cphone; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Reduction Discount Percentage : <?php echo $obj_cn->discount."%"; ?></label>
                                        </div>
                                       
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </div>
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Add Items</h2>
                            <div class="common-content-block">
                                <div class="box box-primary"><br>
                                    <form role="form" id="cnadditem" name="cnadditem" enctype="multipart/form-data" method="post" action="formpost/cnadditem.php">
                                        <input type="hidden" name="invid" id="invid" value="<?php echo $obj_cn->invoiceid; ?>"/>
                                        <input type="hidden" name="cnid" id="cnnid" value="<?php echo $this->cnid; ?>"/>
                                        <input type="hidden" name="discount" id="discount" value="<?php echo $obj_cn->discount; ?>"/>
                                        <input type="hidden" name="kgperpc" id="kgperpc" value=""/>
                                        <input type="hidden" name="invqty" id="invqty" value=""/>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-6">
                                                <label>Select Invoice Item</label><br>  
                                                <select id="invitem" name="invitem" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getItemDetails(this.value);">
                                                    <option value="-1">Select Invoice Item</option>
                                                    <?php
                                                    foreach ($obj_invitems as $item) {
                                                        $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                        $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                        $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                        $itemname = $item->product.$desc1.$desc2.$thickness;
                                                        ?>
                                                        <option value="<?php echo $item->id . "::" .$item->product_id; ?>"><?php echo $itemname; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                            <div class="col-md-6">
                                            <label>Today's Item Base Rate</label><br/>
                                            <input type="text" id="baserate" name="baserate" readonly="true" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("baserate"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                        </div> 
                                        <div class="col-md-12"><br>
                                            <div class="col-md-4">
                                            <label>Batchcode</label><br/>
                                            <input type="text" id="batchcode" name="batchcode" class="form-control" placeholder="batchcode" value="<?php echo $this->getFieldValue("batchcode"); ?>" required="" readonly="">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Qty in MT</label><br/>
                                            <input type="text" id="qty" name="qty" class="form-control" placeholder="MT" value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup=checkqty(this.value); required="">
                                        </div>
                                        <div class="col-md-4">
                                            <label>No. Of units</label><br/>
                                            <input type="text" id="numberofunits" name="numberofunits" readonly="true" class="form-control" placeholder="No. Of Units" readonly value="<?php echo $this->getFieldValue("numberofunits"); ?>"/>
                                        </div>



                                    </div>
                                    <div class="col-md-12"><br/>
                                        <div class="col-md-4">
                                            <label>Invoice Base Rate</label><br/>
                                            <input type="text" id="invbaserate" name="invbaserate" readonly="true" class="form-control" placeholder="Invoice Base Rate" value="<?php echo $this->getFieldValue("baserate"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <label>Invoice Rate</label><br/>
                                            <input type="text" id="invrate" name="invrate" readonly="true" class="form-control" placeholder="Invoice Rate" value="<?php echo $this->getFieldValue("rate"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                                                               
                                        <div class="col-md-4">
                                            <label>Invoice Item Value</label><br/>
                                            <input type="text" id="invvalue" name="invvalue" readonly="true" class="form-control" placeholder="Invoice Item Value" value="<?php echo $this->getFieldValue("value"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                    </div>
                                    <div class="col-md-12"><br>
                                        <div class="col-md-4">
                                            <label>Base Rate after discount</label><br/>
                                            <input type="text" id="discrate" name="discrate" readonly="true" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("baserate"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <label>Rate</label><br/>
                                            <input type="text" id="rate" name="rate" readonly="true" class="form-control" placeholder="Rate" value="<?php echo $this->getFieldValue("rate"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                                                               
                                        <div class="col-md-4">
                                            <label>Value</label><br/>
                                            <input type="text" id="value" name="value" readonly="true" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("value"); ?>" onkeyup=calcValue(this.value); required="">
                                        </div>
                                    </div>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary">Add Item</button>
                                            </div>
                                        </div>
                                         <?php //if ($formResult->form_id == 'createdc') {  ?>
                                        <div class="alert" style="display:<?php echo $formResult->showhide; ?>;"<?php echo $formResult->status; ?>>
                                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4> <?php echo $formResult->status; ?>
                                        </div>
                                            <?php //}  ?>
                                    </form>                                 
                                </div>  
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Sl.No.</td>
                                                <td>Product</td>
<!--                                                <td>Batchcode</td>-->
                                                <td>MRP</td>
                                                <td>MT</td>
                                                <td>Base Rate(Rs./Kg)</td>
                                                <td>Taxable(Rs)</td>
                                                <td>Tax Value(Rs)</td>
                                                <td>Total(Rs)</td>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $slno = 0;
                                            $total_qty = 0;
                                            $total_rate = 0;
                                            $total_taxable = 0;
                                            $total_tax = 0;
                                            $total = 0;
                                            $itemCount = 0;
                                            if ($obj_cnitems != NULL) {
                                                foreach ($obj_cnitems as $item) {
                                                    $slno++;
                                                    $itemCount++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $slno; ?></td>
                                                        <td><?php
                                                             $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                             $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                             $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                             $itemname = $item->product.$desc1.$desc2.$thickness;
                                                               //echo $itemname;
                                                        echo $itemname; ?></br><b><?php echo $item->batchcode; ?></b></td>
<!--                                                        <td><?php echo $item->batchcode; ?></td>-->
                                                        <td><?php echo $item->mrp; ?></td>
                                                        <td><?php echo $item->qty; ?></td>
                                                        <td><?php echo $item->rate; ?></td>
                                                        <?php
                                                        
                                                        $taxable = sprintf("%.2f",$item->rate * $item->qty); 
                                                        $taxTotal =  sprintf("%.2f",$item->igstval * $item->qty);
                                                        
                                                        ?>
                                                        <td><?php echo $taxable; ?></td>
                                                        <td><?php echo $taxTotal; ?></td>
                                                        <td><?php echo $item->total; ?></td>
                                                        <td><button class="btn btn-primary" onclick="deleteCreditNoteItem(<?php echo $item->id; ?>);">Delete</button></td>
                                                    </tr>
                                                    <?php
                                                    $total_qty = $total_qty + $item->qty;
                                                    $total_rate = $total_rate + $item->rate;
                                                    $total_taxable = $total_taxable + $taxable;
                                                    $total_tax = $total_tax + $taxTotal;
                                                    $total = $total + $item->total;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        <tfooter>
                                            <tr>
                                                <td></td>
                                                <td></td>
<!--                                                <td></td>-->
                                                <td></td>
                                                <td><?php echo $total_qty; ?></td>
                                                <td><?php echo $total_rate; ?></td>
                                                <td><?php echo $total_taxable; ?></td>
                                                <td><?php echo $total_tax; ?></td>
                                                <td><?php echo $total; ?></td>
                                                <td></td>
                                            </tr>
                                        </tfooter>
                                    </table>
                                </div>
                                
                               <div class="col-md-12">
                                   <form role="form" id="submitcn" name="submitcn" enctype="multipart/form-data" method="post" action="formpost/submitCreditNote.php">
                                       <input type="hidden" name="itemcount" id="itemcount" value="<?php echo $itemCount; ?>"/>
                                       <input type="hidden" name="cnid" id="cnid" value="<?php echo $this->cnid; ?>"/>
                                
                                        <div class="col-md-12">
                                            <label>&nbsp;</label><br/>
                                            <button type="submit" class="btn btn-primary">Save and Submit</button>
                                        </div>
                                    </form>
                                </div> 
    
                            </div>
                        </div>
                    </div> <!--Add Items>   
                </div>
            </div> 
        </div><!-- end -->
          

        <?php
        // }else{ print "You are not authorized to access this page";}
    }

}
?>


