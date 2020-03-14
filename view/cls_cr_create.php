<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class   cls_cr_create extends cls_renderer{

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
            $menuitem = "rfc";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
//            print_r($formResult);
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create CR</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="createcr" name="createcr" enctype="multipart/form-data" method="post" action="formpost/createCR.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createcr">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="text" id="dispname" name="dispname" class="form-control" placeholder="CR Name" value="<?php echo $this->getFieldValue("dispname"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="rfcname" name="rfcname" class="form-control" placeholder="RFC Name" value="<?php echo $this->getFieldValue("rfcname"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="cntper" name="cntper" class="form-control" placeholder="Contact Person" value="<?php echo $this->getFieldValue("cntper"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <textarea  id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $this->getFieldValue("address"); ?>"></textarea>
<!--                                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php // echo $this->getFieldValue("address"); ?>">-->
                                        </div>
                                        <div class="form-group">
                                            <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?php echo $this->getFieldValue("email"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone" value="<?php echo $this->getFieldValue("phone"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="gstno" name="gstno" class="form-control" placeholder="GST No" value="<?php echo $this->getFieldValue("gstno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="panno" name="panno" class="form-control" placeholder="PAN No" value="<?php echo $this->getFieldValue("panno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="custname" name="custname" class="form-control" placeholder="Customer Name" value="<?php echo $this->getFieldValue("custname"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="custphone" name="custphone" class="form-control" placeholder="Customer Phone" value="<?php echo $this->getFieldValue("custphone"); ?>">
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
                                            <label>Set Auto Price Carryover</label><br>
                                            <input type="radio" name="set" value="1" checked="checked" />Set
                                            <input type="radio" name="set" value="0" />Unset
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createcr') { ?>
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


