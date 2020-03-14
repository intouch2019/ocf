<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_dc_create extends cls_renderer{

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
    
    $(document).ready( function() {
    	$(document).on('change', '.btn-file :file', function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
		    
		    var input = $(this).parents('.input-group').find(':text'),
		        log = label;
		    
		    if( input.length ) {
		        input.val(log);
		    } else {
		        if( log ) alert(log);
		    }
	    
		});
		function readURL(input) {
		    if (input.files && input.files[0]) {
		        var reader = new FileReader();
		        
		        reader.onload = function (e) {
		            $('#img-upload').attr('src', e.target.result);
		        }
		        
		        reader.readAsDataURL(input.files[0]);
		    }
		}

		$("#imgInp").change(function(){
		    readURL(this);
		}); 	
	});


</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "dc";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Distribution Center</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="createdc" name="createdc" enctype="multipart/form-data" method="post" action="formpost/createdc.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createdc">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="DC Name" value="<?php echo $this->getFieldValue("name"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="contact_person" name="contact_person" class="form-control" placeholder="Contact Person" value="<?php echo $this->getFieldValue("contact_person"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $this->getFieldValue("address"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <select id="statesel" name="statesel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                                <option value="">Select State</option>
                                                <?php foreach($obj_states as $state){ ?>
                                                    <option value="<?php echo $state->ID;?>"><?php echo $state->STATE." [ ".$state->STATE_CODE." ]";?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone" value="<?php echo $this->getFieldValue("phone"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="email" name="email" class="form-control" placeholder="Email" value="<?php echo $this->getFieldValue("email"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="gstno" name="gstno" class="form-control" placeholder="GST No" value="<?php echo $this->getFieldValue("gstno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="panno" name="panno" class="form-control" placeholder="PAN No" value="<?php echo $this->getFieldValue("panno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            Select PAN image to upload:
                                            <input type="file" name="fileToUpload" value="fileToUpload" id="fileToUpload">
<!--                                            <input type="submit" value="Upload Image" name="submit">-->
                                        </div>
                                        
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createdc') { ?>
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


