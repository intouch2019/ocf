<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once 'lib/locations/clsLocation.php';
require_once 'lib/user/clsUser.php';

class cls_locfunc_user extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $uid;
        
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
            $this->currStore = getCurrStore();
        
            $this->params = $params;

            if ($params && isset($params['uid'])) { 
                $this->uid = $params['uid'];
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
<script type="text/javascript">   
function moveToRightOrLeft(side){
  var listLeft=document.getElementById('selectLeft');
  var listRight=document.getElementById('selectRight');

  if(side==1){
    if(listLeft.options.length==0){
    alert('You have already moved all fields to Right');
    return false;
    }else{
      var selectedCountry=listLeft.options.selectedIndex;
//      alert(selectedCountry);
      if ( $("#listRight option[value=selectedCountry]").length > 0 ){
          alert("option  exist!");
     }
      move(listRight,listLeft.options[selectedCountry].value,listLeft.options[selectedCountry].text);
      listLeft.remove(selectedCountry);

      if(listLeft.options.length>0){
      listLeft.options[selectedCountry].selected=true;
      }
    }
  } else if(side==2){
    if(listRight.options.length==0){
      alert('You have already moved all fields to Left');
      return false;
    }else{
      var selectedCountry=listRight.options.selectedIndex;
      //alert(listRight.options[selectedCountry].value);      
         move(listLeft,listRight.options[selectedCountry].value,listRight.options[selectedCountry].text);    
     // if(is_numeric(listRight.options[selectedCountry].value)){   
        listRight.remove(selectedCountry);
      //}

      if(listRight.options.length>0){
        listRight.options[selectedCountry].selected=true;
      }
    }
  }
}

function move(listBoxTo,optionValue,optionDisplayText){
  var newOption = document.createElement("option"); 
  newOption.value = optionValue; 
  newOption.text = optionDisplayText; 
  newOption.selected = true;
  listBoxTo.add(newOption, null); 
  return true; 
}

function moveAllLeftToRight(){
   //alert("here");
   var listLeft=document.getElementById('selectLeft');
   var listRight=document.getElementById('selectRight');       
    var selectIndex = 0;
    var l = listLeft.options.length;
    for(var i=0; i <= l ; i++){   
        if(listLeft.options[selectIndex].value==''){  selectIndex=selectIndex+1; continue;}   
        move(listRight,listLeft.options[selectIndex].value,listLeft.options[selectIndex].text);
        listLeft.remove(selectIndex);       
    }
}

function moveAllRightToLeft(){
   //alert("here");
    var listLeft=document.getElementById('selectLeft');
    var listRight=document.getElementById('selectRight');       
    var selectIndex = 0;
    var l = listRight.options.length;
    //alert(l);
    for(var i=0; i <= l ; i++){   
        if(listRight.options[selectIndex].value==''){  selectIndex=selectIndex+1; continue;}   
        
        var sval = listRight.options[selectIndex].value;  
    //alert(sval);
        var asarr = sval.split('<>');

//       if(asarr[1]== "1"){       
//           alert('Default fields wont Left');
//          return false;
//            selectIndex=selectIndex+1;
//            continue;
//       }else{                            
        move(listLeft,listRight.options[selectIndex].value,listRight.options[selectIndex].text);
        listRight.remove(selectIndex);
//       }
    }
}



function getByLocSelect(loc_id){
    var userid = $("#seluser").val();
    //alert(userid);
    if(userid == ""){
        alert("Please select a user first");
    }else{
        var ajaxUrl = "ajax/getUserAssgnLFuncPgs.php?userid="+userid+"&locationid="+loc_id;
//        alert(ajaxUrl);
        $.getJSON(ajaxUrl, function(data) {
            //$("#selectLeft").empty();
            $("#selectRight").empty();
            for (var i = 0; i < data.length; i++) {              
                var arr = data[i].split('::');               
               //$("#selectLeft").append('<option value='+arr[0]+'>'+arr[2]+'</option>');
               $("#selectRight").append('<option value='+arr[0]+'>'+arr[2]+'</option>');
            }            
        }); 
        
        var ajaxPUrl = "ajax/getUserNotAssgndLocFuncPgs.php?userid="+userid+"&locationid="+loc_id;
//        alert(ajaxPUrl);
        $.getJSON(ajaxPUrl, function(data) {
           //$("#selectRight").empty();
            $("#selectLeft").empty();
            for (var i = 0; i < data.length; i++) {               
                var arr = data[i].split('::');                
                //$("#selectRight").append('<option value='+arr[0]+'>'+arr[2]+'</option>');
                $("#selectLeft").append('<option value='+arr[0]+'>'+arr[2]+'</option>');
            }            
        });  
    }    
}


function getByUserSelect(userid){
    $("#sellocation").val("");
    $("#selectLeft").empty();
    $("#selectRight").empty();   
}

function assignPg() {        
       $('#selectLeft option').attr('selected', 'selected');
       $('#selectRight option').attr('selected', 'selected');
       var sellocation = $('#sellocation').val();
       var seluser = $('#seluser').val();
       if (sellocation !="") {
                //var multiplevalues = $('#selectLeft').val();
                 var multiplevalues = $('#selectRight').val();
//                alert(multiplevalues);
                //var multiplevalues2 = $('#selectRight').val();
                 var multiplevalues2 = $('#selectLeft').val();
                 var form_id = $('#form_id').val();
//                alert(multiplevalues2);
                //alert("formpost/assignLFuncsToUser.php?user_id="+seluser+"&location_id="+sellocation+"&to_enable_pgs="+multiplevalues+"&to_disable_pgs="+multiplevalues2+"&form_id="+form_id);
                window.location.href="formpost/assignLFuncsToUser.php?user_id="+seluser+"&location_id="+sellocation+"&to_enable_pgs="+multiplevalues+"&to_disable_pgs="+multiplevalues2+"&form_id="+form_id;                           
       } else {
           alert("please select a user to assign pages");
       }
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<!--<link rel="stylesheet" href="js/chosen/chosen.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $formResult = $this->getFormResult();
            $menuitem = "func_to_loc";//pagecode
            include "sidemenu.php";    
            $clsLocation = new clsLocation();
            $clsUser = new clsUser();
            //
//                        include "sidemenu.".$this->currStore->usertype.".php";    
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>
<div class="container-section">
    <div class="row">        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                <h2 class="title-bar">Assign Location Functionality to User</h2> 
                <div class="row">
                    <form  id="locFuncForm" name="locFuncForm" method="" onsubmit="assignPg(); return false;" > <!--action="formpost/assignFuncsToLocation.php"-->
                        <input type = "hidden" name="form_id" id="form_id" value="locFuncForm">
                        <div class="col-md-6">
                        <div class="form-group">
                         <select id="seluser" name="seluser" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getByUserSelect(this.value);" >
                                         <?php $allusers = $clsUser->getAllActiveUsers();
                                         if(!empty($allusers)){
                                             ?>
                                             <option value="">Select User</option>
                                         <?php
                                             foreach($allusers as $user){ 
                                                 if(isset($user) && !empty($user) && $user != null){
                                                     $selected = "";
                                                     if($user->id == $this->uid){
                                                         $selected = "selected";
                                                     }
                                         ?>
                                         <option value="<?php echo $user->id;?>" <?php echo $selected; ?>><?php echo $user->name;?></option>
                                                 <?php     } }
                                         }
                         ?>

                                     </select>
                        </div>    
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                         <select id="sellocation" name="sellocation" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getByLocSelect(this.value);">
                                         <?php $locations = $clsLocation->getLocations();
                                         if(!empty($locations)){
                                             ?>
                                             <option value="">Select Location</option>
                                         <?php
                                             foreach($locations as $loc){ 
                                                 if(isset($loc) && !empty($loc) && $loc != null){
                                         ?>
                                         <option value="<?php echo $loc->id;?>"><?php echo $loc->name;?></option>
                                                 <?php     } }
                                         }
                         ?>

                                     </select>
                        </div>    
                    </div>
                    <br>
                    <div class="col-md-5">
                        <div class="form-group">
                             Disabled Functionalities
                             <select name="selectLeft"   multiple size="10" style="width:100%;"   id="selectLeft"> <!--style="width:200px;"-->
                              </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            &nbsp;<br>
                            <button type="button" class="btn btn-primary" name="btnRight"  id="btnRight"  onClick="javaScript:moveToRightOrLeft(1);">&gt</button>                            
                            <br/><br/>
                            <button type="button" class="btn btn-primary" name="btnLeft" type="button" id="btnLeft"  onClick="javaScript:moveToRightOrLeft(2);">&lt</button>                            
                            <br/><br/>                            
                            <button type="button" class="btn btn-primary" name="btnLeftToRight" type="button" id="btnLeftToRight" onClick="javaScript:moveAllLeftToRight();">&gt&gt</button>                        
                            <br/><br/>
                            <button type="button" class="btn btn-primary" name="btnRightToLeft" type="button" id="btnRightToLeft" onClick="javaScript:moveAllRightToLeft();">&lt&lt</button>                            
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            Enabled Functionalities
                            <select name="selectRight" multiple size="10" style="width:100%;" id="selectRight">   <!-- class="selectpicker" style="width:200px;"-->                                    
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </div>
                    </form>
                    <div class="col-md-12">
                       <?php if ($formResult->form_id == 'locFuncForm') { ?> 
                      <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4> <?php echo $formResult->status; ?>                              
                      </div> 
                       <?php 
                            unset($_SESSION['form_id']);
                       } ?>
                     </div> 
                    </div><!--row closing-->                   
               </div> <!--panel body closing-->
            </div>  <!--panel closing-->  
     </div> <!--col -12 closing -->
 </div> <!--row closing-->
<!--<script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>    -->
</div> <!--container closing-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


