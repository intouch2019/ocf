<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once ("lib/locations/clsLocation.php");

class cls_create_location extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $wflag="";
        
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
            $this->currStore = getCurrStore();       
//        $this->params = $params;
        if($params && isset($params['wflag'])){
                 $this->wflag = $params['wflag']; 
//                 print $this->productid;
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
	
	************************************************************************************************************/	

</script> -->
<script type="text/javaScript">              
    $(function(){
      // $("#d_hub_sel").hide();
       $("#is_dependant").click(function(){                    
        //  $("#d_hub_sel").show();
         if($(this).is(':checked')){
            // alert("checked");
             $("#d_hub_sel").css("visibility", "visible");
         }else{
             $("#d_hub_sel").css("visibility", "hidden");
         }
         
       }); 
       <?php if(isset($this->wflag) && trim($this->wflag)==1){ ?>
             $('#day').show();   
       <?php }else{ ?>
             $('#day').hide();
       <?php } ?> 
     
          $(".chkbox").click(function(){   
              //alert($(this).attr('id'));
              var id = $(this).attr('id');
              var arr = id.split("_");
             // alert(arr[0]+" "+arr[1]);
              var txtid = arr[0]+"_txtbx";
             // alert(txtid);
              
        //  $("#d_hub_sel").show();
         if($(this).is(':checked')){
            // alert("checked");
            $("#"+txtid).prop('disabled', false); 
            $("#"+txtid).focus();
         }else{
            $("#"+txtid).prop('disabled', true); 
            $("#"+txtid).val("");
         }
         
       });
    });

function showweekdays(selected){
//    alert(selected);
      if(selected == 1){ //1=event
           $('#day').show();
      }else{
           $('#day').hide();
      }
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "loc";//pagecode
            include "sidemenu.php";   
            $formResult = $this->getFormResult();
            $clsLocation = new clsLocation();
//            $l = "weekday[1][txtbox]";
//            $vl = $this->getFieldValue($l);
//            print "<br> VALUE: ".$vl;
//        print_r($_SESSION);
//            print_r($_SESSION['form_post']);
//            print_r($formResult);
//                        include "sidemenu.".$this->currStore->usertype.".php";    
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create Location</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary"><br>
                                <form role="form" id="createloc" name="createloc"  method="post" action="formpost/create_location.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="createloc">
                                    <div class="box-body">
                                         <div class="form-group">                                                                                        
                                             <select id="selltype"  name="selltype" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="showweekdays(this.value);">
                                                 <option value="">Select Location Type</option>
                                                 <?php
                                                $lTypeObjs = $clsLocation->getLocationTypes();
                                                if(!empty($lTypeObjs)){
                                                    foreach($lTypeObjs as $lTypeObj){
                                                        if(isset($lTypeObj) && !empty($lTypeObj) && $lTypeObj != null){
                                                            $selected = "";
                                                            if($lTypeObj->id == 3){ continue; } // online
                                                            $stype = $this->getFieldValue("selltype");
                                                            if($stype == $lTypeObj->id){
                                                                $selected = "selected";
                                                            }
                                                            ?>
                                                 <option value="<?php echo $lTypeObj->id; ?>" <?php echo $selected; ?>><?php echo $lTypeObj->name; ?></option>    
                                                    <?php        
                                                        }
                                                    }
                                                }
                                            ?>
                                            </select>     
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="<?php echo $this->getFieldValue("name"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="lcode" name="lcode" class="form-control" placeholder="Location Code" value="<?php echo $this->getFieldValue("lcode"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $this->getFieldValue("address"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="city" name="city" class="form-control" placeholder="City" value="<?php echo $this->getFieldValue("city"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="pincode" name="pincode" class="form-control" placeholder="Pincode" value="<?php echo $this->getFieldValue("pincode"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <?php 
                                                $checked = "";$visibility="hidden";
                                                if(isset($_SESSION['d_hub_err']) && trim($_SESSION['d_hub_err']) == "1"){
                                                    $checked = "checked";
                                                    $visibility = "visible";
                                                }
                                            ?>
                                            <input type="checkbox" id="is_dependant" name="is_dependant"   <?php echo $checked; ?>>&nbsp;&nbsp;Is Dependant
                                        </div>
                                        <div id="d_hub_sel" class="form-group" style="visibility:<?php echo $visibility; ?>;" >
                                            <select name="selhub" id="selhub"  class="selectpicker form-control" data-show-subtext="true" data-live-search="true" >
                                                <option value="">Select Hub</option>
                                                <?php
                                                $hubTypeObjs = $clsLocation->getHubLocations();
                                                if(!empty($hubTypeObjs)){
                                                    foreach($hubTypeObjs as $hubTypeObj){
                                                        if(isset($hubTypeObj) && !empty($hubTypeObj) && $hubTypeObj != null){
                                                            $selected = "";
                                                            if(isset($_SESSION['d_hub_val'])){
                                                                if($hubTypeObj->id == $_SESSION['d_hub_val']){
                                                                    $selected ="selected";
                                                                }
                                                            }
                                                            ?>
                                                 <option value="<?php echo $hubTypeObj->id; ?>"<?php echo $selected; ?>><?php echo $hubTypeObj->name; ?></option>    
                                                    <?php        
                                                        }
                                                    }
                                                }
                                            ?>
                                            </select>
                                                
                                        </div>
                                    </div>
                                    <div class="form-group" id="day">
                                         <div class="panel panel-default">
                                            <div class="panel-heading">Week Days</div>
                                            <div class="panel-body">
                                                <div class="row">                                                                                                        
                                                    <div class="col-sm-3">
                                                        <?php
                                                            $sun_chkbox = "weekday[".Weekdays::sunday."][chkbox]";
                                                            $sun_txtbox = "weekday[".Weekdays::sunday."][txtbox]";
                                                            
                                                            $mon_chkbox = "weekday[".Weekdays::monday."][chkbox]";
                                                            $mon_txtbox = "weekday[".Weekdays::monday."][txtbox]";
                                                            
                                                            $tue_chkbox = "weekday[".Weekdays::tuesday."][chkbox]";
                                                            $tue_txtbox = "weekday[".Weekdays::tuesday."][txtbox]";
                                                            
                                                            $wed_chkbox = "weekday[".Weekdays::wednesday."][chkbox]";
                                                            $wed_txtbox = "weekday[".Weekdays::wednesday."][txtbox]";
                                                            
                                                            $thur_chkbox = "weekday[".Weekdays::thursday."][chkbox]";
                                                            $thur_txtbox = "weekday[".Weekdays::thursday."][txtbox]";
                                                            
                                                            $fri_chkbox = "weekday[".Weekdays::friday."][chkbox]";
                                                            $fri_txtbox = "weekday[".Weekdays::friday."][txtbox]";
                                                            
                                                            $sat_chkbox = "weekday[".Weekdays::saturday."][chkbox]";
                                                            $sat_txtbox = "weekday[".Weekdays::saturday."][txtbox]";
                                                            
                                                        ?>
                                                        <input type="checkbox" id="sun_chkbx" name="<?php echo $sun_chkbox; ?>" class="chkbox"<?php if((isset($_SESSION[Weekdays::sunday."_error"]) && trim($_SESSION[Weekdays::sunday."_error"])==1) || (isset($_SESSION[Weekdays::sunday."_txtboxval"]) && trim($_SESSION[Weekdays::sunday."_txtboxval"]!=""))){ echo "checked"; } ?>>Sunday
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" id="sun_txtbx" name="<?php echo $sun_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                            <?php if(isset($_SESSION[Weekdays::sunday."_error"]) && trim($_SESSION[Weekdays::sunday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::sunday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::sunday."_txtboxval"]) && trim($_SESSION[Weekdays::sunday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::sunday."_txtboxval"];} ?>"> 
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="checkbox" id="thurs_chkbx" name="<?php echo $thur_chkbox; ?>" class="chkbox" <?php if((isset($_SESSION[Weekdays::thursday."_error"]) && trim($_SESSION[Weekdays::thursday."_error"])==1) || (isset($_SESSION[Weekdays::thursday."_txtboxval"]) && trim($_SESSION[Weekdays::thursday."_txtboxval"]!=""))){ echo "checked"; } ?>>Thursday
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" id="thurs_txtbx" name="<?php echo $thur_txtbox; ?>" placeholder="Enter Time" size="8%" <?php if(isset($_SESSION[Weekdays::thursday."_error"]) && trim($_SESSION[Weekdays::thursday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::thursday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::thursday."_txtboxval"]) && trim($_SESSION[Weekdays::thursday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::thursday."_txtboxval"];} ?>"> 
                                                    </div>                                                    
                                                </div> 
                                                <div class="row">                                                                                                        
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" id="mon_chkbx" name="<?php echo $mon_chkbox; ?>" class="chkbox" <?php if((isset($_SESSION[Weekdays::monday."_error"]) && trim($_SESSION[Weekdays::monday."_error"])==1) || (isset($_SESSION[Weekdays::monday."_txtboxval"]) && trim($_SESSION[Weekdays::monday."_txtboxval"]!=""))){ echo "checked"; } ?>>Monday
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" id="mon_txtbx" name="<?php echo $mon_txtbox; ?>" placeholder="Enter Time" size="8%" <?php if(isset($_SESSION[Weekdays::monday."_error"]) && trim($_SESSION[Weekdays::monday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::monday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::monday."_txtboxval"]) && trim($_SESSION[Weekdays::monday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::monday."_txtboxval"];} ?>"> 
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="checkbox" id="fri_chkbx" name="<?php echo $fri_chkbox; ?>" class="chkbox" <?php if((isset($_SESSION[Weekdays::friday."_error"]) && trim($_SESSION[Weekdays::friday."_error"])==1) || (isset($_SESSION[Weekdays::friday."_txtboxval"]) && trim($_SESSION[Weekdays::friday."_txtboxval"]!=""))){ echo "checked"; } ?>>Friday
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" id="fri_txtbx" name="<?php echo $fri_txtbox; ?>" placeholder="Enter Time" size="8%" <?php if(isset($_SESSION[Weekdays::friday."_error"]) && trim($_SESSION[Weekdays::friday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::friday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::friday."_txtboxval"]) && trim($_SESSION[Weekdays::friday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::friday."_txtboxval"];} ?>"> 
                                                    </div>                                                    
                                                </div>
                                                <div class="row">                                                                                                        
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" id="tues_chkbx" name="<?php echo $tue_chkbox; ?>" class="chkbox" <?php if((isset($_SESSION[Weekdays::tuesday."_error"]) && trim($_SESSION[Weekdays::tuesday."_error"])==1) || (isset($_SESSION[Weekdays::tuesday."_txtboxval"]) && trim($_SESSION[Weekdays::tuesday."_txtboxval"]!=""))){ echo "checked"; } ?>>Tuesday
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" id="tues_txtbx" name="<?php echo $tue_txtbox; ?>" placeholder="Enter Time" size="8%" <?php if(isset($_SESSION[Weekdays::tuesday."_error"]) && trim($_SESSION[Weekdays::tuesday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::tuesday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::tuesday."_txtboxval"]) && trim($_SESSION[Weekdays::tuesday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::tuesday."_txtboxval"];} ?>"> 
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <input type="checkbox" id="sat_chkbx" name="<?php echo $sat_chkbox; ?>" class="chkbox" <?php if((isset($_SESSION[Weekdays::saturday."_error"]) && trim($_SESSION[Weekdays::saturday."_error"])==1) || (isset($_SESSION[Weekdays::saturday."_txtboxval"]) && trim($_SESSION[Weekdays::saturday."_txtboxval"]!=""))){ echo "checked"; } ?>>Saturday
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" id="sat_txtbx" name="<?php echo $sat_txtbox; ?>" placeholder="Enter Time" size="8%" <?php if(isset($_SESSION[Weekdays::saturday."_error"]) && trim($_SESSION[Weekdays::saturday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::saturday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::saturday."_txtboxval"]) && trim($_SESSION[Weekdays::saturday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::saturday."_txtboxval"];} ?>"> 
                                                    </div>                                                    
                                                </div> 
                                                <div class="row">                                                                                                        
                                                    <div class="col-sm-3">
                                                        <input type="checkbox" id="wed_chkbx" name="<?php echo $wed_chkbox; ?>" class="chkbox" <?php if((isset($_SESSION[Weekdays::wednesday."_error"]) && trim($_SESSION[Weekdays::wednesday."_error"])==1) || (isset($_SESSION[Weekdays::wednesday."_txtboxval"]) && trim($_SESSION[Weekdays::wednesday."_txtboxval"]!=""))){ echo "checked"; } ?>>Wednesday
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" id="wed_txtbx" name="<?php echo $wed_txtbox; ?>" placeholder="Enter Time" size="8%" <?php if(isset($_SESSION[Weekdays::wednesday."_error"]) && trim($_SESSION[Weekdays::wednesday."_error"])==1){  
                                                                //do nothing
                                                            }else{ if(! isset($_SESSION[Weekdays::wednesday."_txtboxval"])){echo "disabled=true";} } ?> 
                                                            value="<?php if(isset($_SESSION[Weekdays::wednesday."_txtboxval"]) && trim($_SESSION[Weekdays::wednesday."_txtboxval"]!="")){ echo $_SESSION[Weekdays::wednesday."_txtboxval"];} ?>"> 
                                                    </div>                                                                                                       
                                                </div> 
                                            </div>
                                         </div>                                            
                                        </div>
                                    
                                    <div class="box-footer">
                                        <!--<input type="submit" class="btn-primary" style="width:150px;height: 30px;" value="Create">-->
                                        <!--<div class="col-xs-4 col-md-2 col-md-push-3">-->
                                          <button type="submit" class="btn btn-primary">Create</button>
                                        <!--</div>-->
                                    </div>
                                </form><br><br>
                                <?php if ($formResult->form_id == 'createloc') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php  } ?>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div> 
 </div>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


