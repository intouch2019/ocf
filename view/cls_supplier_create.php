<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_supplier_create extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
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
   
    $(function () {   
        $('#dateofentry').datepicker({
             format:'dd-mm-yyyy'
         });
        
        $('#gstapp').change(function() {
          if($(this).prop('checked')){
              $("#gstdiv").show();
          }else{
              $("#gstno").val("");
              $("#gstdiv").hide();
          }
        })         
    }); 
     
    function setCtg(ctgValue){
        if(ctgValue == -1){
            $("#addctg").show();
        }else{
            $("#addctg").hide();
        }
    }

    function setSpec(specValue){
        if(specValue == -1){
            $("#addspec").show();
        }else{
            $("#addctg").hide();
        }
    } 
    
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "suppliers";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            $obj_firm_types = $dbl->getFirmTypes();
            $obj_currencies = $dbl->getAllCurrencies();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Supplier</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="createsupplier" name="createsupplier" enctype="multipart/form-data" method="post" action="formpost/createsupplier.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createsupplier">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="dateofentry" placeholder="Date of Entry" name="dateofentry" value=""/>
                                            <!--<input type="text"   class="form-control"  value="<?php echo $this->getFieldValue("dateofentry"); ?>">-->
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="kycnumber" name="kycnumber" class="form-control" placeholder="KYC Reference" value="<?php echo $this->getFieldValue("kycnumber"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="companyname" name="companyname" class="form-control" placeholder="Company Name" value="<?php echo $this->getFieldValue("companyname"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="bankname" name="bankname" class="form-control" placeholder="Bank Name" value="<?php echo $this->getFieldValue("bankname"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="bankaccno" name="bankaccno" class="form-control" placeholder="Bank A/C No" value="<?php echo $this->getFieldValue("bankaccno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="bankbranchname" name="bankbranchname" class="form-control" placeholder="Bank Branch Name" value="<?php echo $this->getFieldValue("bankbranchname"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <select id="firmtype" name="firmtype" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                                <option value="">Select Firm Type</option>
                                                <?php foreach($obj_firm_types as $firm){ ?>
                                                    <option value="<?php echo $firm->id;?>">
                                                        <?php echo $firm->firm_type;?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="country" name="country" class="selectpicker countrypicker" data-live-search="true" data-default="India" data-flag="true">
                                                <option selected="selected">India</option>
                                            </select>                                            
                                        </div>
                                        <div class="form-group">
                                            <select id="currency" name="currency" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                                <option value="">Select Currency</option>
                                                <?php foreach($obj_currencies as $curr){ 
                                                    if($curr->iso_code == "INR"){ $selected = "selected"; }?>
                                                    <option value="<?php echo $curr->id;?>" <?php echo $selected; ?>>
                                                        <?php echo $curr->iso_code;?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="state" name="state" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                                <option value="">Select State</option>
                                                <?php foreach($obj_states as $state){ ?>
                                                    <option value="<?php echo $state->ID;?>"><?php echo $state->STATE." [ ".$state->STATE_CODE." ]";?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="district" name="district" class="form-control" placeholder="District" value="<?php echo $this->getFieldValue("district"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Registered Address" value="<?php echo $this->getFieldValue("address"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="graddress" name="graddress" class="form-control" placeholder="GR Address" value="<?php echo $this->getFieldValue("graddress"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="pincode" name="pincode" class="form-control" placeholder="Pincode" value="<?php echo $this->getFieldValue("pincode"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="panno" name="panno" class="form-control" placeholder="PAN No" value="<?php echo $this->getFieldValue("panno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="cinno" name="cinno" class="form-control" placeholder="CIN No" value="<?php echo $this->getFieldValue("cinno"); ?>">
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" id="gstapp" name="gstapp">Is GST Applicable</label>
                                        </div>
                                        <div class="form-group" id="gstdiv" style="display:none;">
                                            <input type="text" id="gstno" name="gstno" class="form-control" placeholder="GST No" value="<?php echo $this->getFieldValue("gstno"); ?>">
                                        </div>
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" id="contactperson1" name="contactperson1" class="form-control" placeholder="Contact Person 1" value="<?php echo $this->getFieldValue("contactperson1"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="phone1" name="phone1" class="form-control" placeholder="Phone 1" value="<?php echo $this->getFieldValue("phone1"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="email1" name="email1" class="form-control" placeholder="Email 1" value="<?php echo $this->getFieldValue("email1"); ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" id="contactperson2" name="contactperson2" class="form-control" placeholder="Contact Person 2" value="<?php echo $this->getFieldValue("contactperson2"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="phone2" name="phone2" class="form-control" placeholder="Phone 2" value="<?php echo $this->getFieldValue("phone2"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="email2" name="email2" class="form-control" placeholder="Email 2" value="<?php echo $this->getFieldValue("email2"); ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" id="contactperson3" name="contactperson3" class="form-control" placeholder="Contact Person 3" value="<?php echo $this->getFieldValue("contactperson3"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="phone3" name="phone3" class="form-control" placeholder="Phone 3" value="<?php echo $this->getFieldValue("phone3"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="email3" name="email3" class="form-control" placeholder="Email 3" value="<?php echo $this->getFieldValue("email3"); ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" id="contactperson4" name="contactperson4" class="form-control" placeholder="Contact Person 4" value="<?php echo $this->getFieldValue("contactperson4"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="phone4" name="phone4" class="form-control" placeholder="Phone 4" value="<?php echo $this->getFieldValue("phone4"); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" id="email4" name="email4" class="form-control" placeholder="Email 4" value="<?php echo $this->getFieldValue("email4"); ?>">
                                                </td>
                                            </tr>
                                            </tbody>                                                
                                        </table>
                                        <div class="form-group">
                                            <input type="text" id="msmedno" name="msmedno" class="form-control" placeholder="MSMED Registration No" value="<?php echo $this->getFieldValue("msmedno"); ?>">
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createsupplier') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                             </div>   
                            
                        </div>
                    </div>
                </div>
            </div>
        </div> 
 </div>
<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


