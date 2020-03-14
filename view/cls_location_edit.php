<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once ("lib/locations/clsLocation.php");


class cls_location_edit extends cls_renderer {
    var $params;
    var $currStore;
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
//        $this->params = $params;
        if($params && isset($params['lid'])){
                 $this->locationid = $params['lid']; 
             }
    }

    function extraHeaders() {
    ?>

<script type="text/javascript">  
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
</script> 
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
        $menuitem="loc";
        include "sidemenu.php";
        $db = new DBConn();
        $clsLocation = new clsLocation();
        
        //$query = "select l.* , lt.name as location_type from it_locations l , it_location_types lt where l.location_type_id = lt.id and l.id= $this->locationid ";
        //$lobj = $db->fetchObject($query);
        $lobj = $clsLocation->fetchLocationById($this->locationid);
        if($lobj){
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Edit Location</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form class="form-horizontal" id="editlocationform" name="editlocationform"  method="post" action="formpost/editLocation.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="editlocationform">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label id=ltype" class="col-md-3 control-label">Location Type</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="loctype" name="loctype" value = "<?php echo $lobj->location_type ;?>"  readonly>                                                
                                                <input type="hidden" name="loc_type_id" value="<?php echo $lobj->location_type_id ;?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id=name" class="col-md-3 control-label">Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="locname" name="locname" value = "<?php echo $lobj->name ;?>">
                                                <input type="hidden" name="loc_id" value="<?php echo $lobj->id ;?>"/> 
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label id=lcode" class="col-md-3 control-label">Location Code</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="loccode" name="loccode" value = "<?php echo $lobj->location_code ;?>">                                               
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="add" class="col-md-3 control-label">Address</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="address" name="address"  value = "<?php echo $lobj->address ;?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="ct" class="col-md-3 control-label" >City</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="city" name="city"  value = "<?php echo $lobj->city ;?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="pc" class="col-md-3 control-label" >Pincode</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="pincode" name="pincode"  value = "<?php echo $lobj->pincode ;?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="status" class="col-md-3 control-label" >Status</label>
                                                <div class="col-md-9">
                                                    <input type="radio" class="radio-inline" value="1" id="actvsel" name="actvsel" <?php if($lobj->is_active == 1){ echo "checked"; } ?> > Active
                                                    <input type="radio" class="radio-inline" value="0" id="actvsel" name="actvsel" <?php if($lobj->is_active == 0){ echo "checked"; } ?> > In active
                                                </div>
                                        </div>
                                        <div class="form-group">
                                            <?php                                                
                                                $checked = ""; $visibility="hidden";
                                                if(trim($lobj->is_dependant) == "1"){
                                                    $checked = "checked";
                                                    $visibility = "visible";
                                                }
                                               
                                                if(isset($_SESSION['ed_hub_err']) && trim($_SESSION['ed_hub_err']) == "1"){
                                                    $checked = "checked";
                                                    $visibility = "visible";
                                                }
                                            ?>
                                            <label id="is_dp" class="col-md-3 control-label" >Is Dependant</label>
                                            <div class="col-md-9">
                                              <input type="checkbox" id="is_dependant" name="is_dependant" <?php echo $checked; ?>>
                                            </div>
                                        </div>
                                        <div id="d_hub_sel" class="form-group" style="visibility:<?php echo $visibility; ?>;" >
                                            <label id="dhub" class="col-md-3 control-label" >Dependant Hub</label>
                                            <div class="col-md-9">
                                                <select name="selhub" id="selhub" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                <option value="">Select Hub</option>
                                                <!--<option value="1"> Hub</option>-->
                                                <?php
                                                $hubTypeObjs = $clsLocation->getHubLocations();
                                                if(!empty($hubTypeObjs)){
                                                    foreach($hubTypeObjs as $hubTypeObj){
                                                        if(isset($hubTypeObj) && !empty($hubTypeObj) && $hubTypeObj != null){
                                                            $selected = "";
                                                            //fetch dependant hub
                                                            $dlobj = $clsLocation->fetchDependantHub($lobj->id);
                                                            if($hubTypeObj->id == $dlobj->parent_location_id){
                                                               $selected = "selected"; 
                                                            }
                                                            ?>
                                                 <option value="<?php echo $hubTypeObj->id; ?>" <?php echo $selected; ?>><?php echo $hubTypeObj->name; ?></option>    
                                                    <?php        
                                                        }
                                                    }
                                                }
                                            ?>
                                            </select>
                                            </div>    
                                        </div>
                                        <?php if(trim($lobj->location_type_id)==1){ //event 
                                            $event_info_arr = array();
                                            //fetch entered value
                                            $eventobjs = $clsLocation->fetchEventInfo($lobj->id);
                                            if(!empty($eventobjs)){
                                                foreach($eventobjs as $eventobj){
                                                    if(isset($eventobj) && !empty($eventobj) && $eventobj != null){
                                                        $event_info_arr[$eventobj->day_of_week] = $eventobj->event_time;
                                                    }
                                                }
                                            }
                                        ?>
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
                                                            <input type="checkbox" id="sun_chkbx" name="<?php echo $sun_chkbox; ?>" class="chkbox"
                                                                <?php if(array_key_exists(Weekdays::sunday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::sunday."_ed_error"]) && trim($_SESSION[Weekdays::sunday."_ed_error"])==1) || (isset($_SESSION[Weekdays::sunday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::sunday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Sunday
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="text" id="sun_txtbx" name="<?php echo $sun_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::sunday."_ed_error"]) && trim($_SESSION[Weekdays::sunday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::sunday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::sunday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::sunday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::sunday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::sunday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::sunday, $event_info_arr)){ echo $event_info_arr[Weekdays::sunday]; } ?>"> 
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="checkbox" id="thurs_chkbx" name="<?php echo $thur_chkbox; ?>" class="chkbox" 
                                                                <?php if(array_key_exists(Weekdays::thursday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::thursday."_ed_error"]) && trim($_SESSION[Weekdays::thursday."_ed_error"])==1) || (isset($_SESSION[Weekdays::thursday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::thursday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Thursday
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="text" id="thurs_txtbx" name="<?php echo $thur_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::thursday."_ed_error"]) && trim($_SESSION[Weekdays::thursday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::thursday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::thursday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::thursday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::thursday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::thursday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::thursday, $event_info_arr)){ echo $event_info_arr[Weekdays::thursday]; } ?>"> 
                                                        </div>                                                    
                                                    </div> 
                                                    <div class="row">                                                                                                        
                                                        <div class="col-sm-3">
                                                            <input type="checkbox" id="mon_chkbx" name="<?php echo $mon_chkbox; ?>" class="chkbox" 
                                                                <?php if(array_key_exists(Weekdays::monday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::monday."_ed_error"]) && trim($_SESSION[Weekdays::monday."_ed_error"])==1) || (isset($_SESSION[Weekdays::monday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::monday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Monday
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="text" id="mon_txtbx" name="<?php echo $mon_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::monday."_ed_error"]) && trim($_SESSION[Weekdays::monday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::monday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::monday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::monday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::monday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::monday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::monday, $event_info_arr)){ echo $event_info_arr[Weekdays::monday]; } ?>"> 
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="checkbox" id="fri_chkbx" name="<?php echo $fri_chkbox; ?>" class="chkbox" 
                                                                <?php if(array_key_exists(Weekdays::friday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::friday."_ed_error"]) && trim($_SESSION[Weekdays::friday."_ed_error"])==1) || (isset($_SESSION[Weekdays::friday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::friday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Friday
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="text" id="fri_txtbx" name="<?php echo $fri_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::friday."_ed_error"]) && trim($_SESSION[Weekdays::friday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::friday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::friday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::friday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::friday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::friday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::friday, $event_info_arr)){ echo $event_info_arr[Weekdays::friday]; } ?>"> 
                                                        </div>                                                    
                                                    </div>
                                                    <div class="row">                                                                                                        
                                                        <div class="col-sm-3">
                                                            <input type="checkbox" id="tues_chkbx" name="<?php echo $tue_chkbox; ?>" class="chkbox" 
                                                                <?php if(array_key_exists(Weekdays::tuesday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::tuesday."_ed_error"]) && trim($_SESSION[Weekdays::tuesday."_ed_error"])==1) || (isset($_SESSION[Weekdays::tuesday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::tuesday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Tuesday
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="text" id="tues_txtbx" name="<?php echo $tue_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::tuesday."_ed_error"]) && trim($_SESSION[Weekdays::tuesday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::tuesday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::tuesday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::tuesday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::tuesday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::tuesday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::tuesday, $event_info_arr)){ echo $event_info_arr[Weekdays::tuesday]; } ?>"> 
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <input type="checkbox" id="sat_chkbx" name="<?php echo $sat_chkbox; ?>" class="chkbox" 
                                                                <?php if(array_key_exists(Weekdays::saturday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::saturday."_ed_error"]) && trim($_SESSION[Weekdays::saturday."_ed_error"])==1) || (isset($_SESSION[Weekdays::saturday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::saturday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Saturday
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <input type="text" id="sat_txtbx" name="<?php echo $sat_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::saturday."_ed_error"]) && trim($_SESSION[Weekdays::saturday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::saturday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::saturday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::saturday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::saturday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::saturday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::saturday, $event_info_arr)){ echo $event_info_arr[Weekdays::saturday]; } ?>"> 
                                                        </div>                                                    
                                                    </div> 
                                                    <div class="row">                                                                                                        
                                                        <div class="col-sm-3">
                                                            <input type="checkbox" id="wed_chkbx" name="<?php echo $wed_chkbox; ?>" class="chkbox" 
                                                                <?php if(array_key_exists(Weekdays::wednesday, $event_info_arr)){ echo "checked"; }else { if((isset($_SESSION[Weekdays::wednesday."_ed_error"]) && trim($_SESSION[Weekdays::wednesday."_ed_error"])==1) || (isset($_SESSION[Weekdays::wednesday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::wednesday."_ed_txtboxval"]!="")) ){ echo "checked"; } } ?>>Wednesday
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="text" id="wed_txtbx" name="<?php echo $wed_txtbox; ?>" placeholder="Enter Time" size="8%" 
                                                                <?php if(isset($_SESSION[Weekdays::wednesday."_ed_error"]) && trim($_SESSION[Weekdays::wednesday."_ed_error"])==1){  
                                                                    //do nothing
                                                                }else{ if(! isset($_SESSION[Weekdays::wednesday."_ed_txtboxval"]) && ( ! array_key_exists(Weekdays::wednesday, $event_info_arr))){echo "disabled=true";} } ?> 
                                                                   value="<?php if(isset($_SESSION[Weekdays::wednesday."_ed_txtboxval"]) && trim($_SESSION[Weekdays::wednesday."_ed_txtboxval"]!="")){ echo $_SESSION[Weekdays::wednesday."_ed_txtboxval"];}else if(array_key_exists(Weekdays::wednesday, $event_info_arr)){ echo $event_info_arr[Weekdays::wednesday]; } ?>"> 
                                                        </div>                                                                                                       
                                                    </div> 
                                                </div>
                                             </div>                                            
                                            </div>
                                        <?php  } ?>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'editlocationform') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                    <?php   
                                    if (isset($formResult->cssClass) && $formResult->cssClass == 'success' && !isset($_SESSION['creditnoteupload'])) {                                                        
                                                    ?>
                                        
                                    <?php } ?>   
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
    <?php
        }
    } //pageContent
}//class
?>
