<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_assign_user_roles extends cls_renderer{

        function __construct($params=null) {
            
        }

function extraHeaders() { ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript"> 
    
    function moveToRightOrLeft(side){
  var listLeft=document.getElementById('selectLeft');
  var listRight=document.getElementById('selectRight');

  if(side==1){
    if(listLeft.options.length==0){
    alert('You have already moved all fields to Right');
    return false;
    }else{
      var selectedCountry=listLeft.options.selectedIndex;
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



function getBySelectUser(userid){
//    var userid = $("#seluser").val();
    //alert(userid);
    if(userid == ""){
        alert("Please select a user first");
    }else{
        var ajaxUrl = "ajax/getUserAssignedRoles.php?userid="+userid;
//        alert(ajaxUrl);
        $.getJSON(ajaxUrl, function(data) {
            //$("#selectLeft").empty();
            $("#selectRight").empty();
            for (var i = 0; i < data.length; i++) {
//                alert(data[i]);
                var arr = data[i].split('::');
               $("#selectRight").append('<option value='+arr[0]+'>'+arr[1]+'</option>');
            }            
        }); 
        
        var ajaxPUrl = "ajax/getNotUserAssignedRoles.php?userid="+userid;
//        alert(ajaxPUrl);
        $.getJSON(ajaxPUrl, function(data) {
           //$("#selectRight").empty();
            $("#selectLeft").empty();
            for (var i = 0; i < data.length; i++) {               
                var arr = data[i].split('::');
                $("#selectLeft").append('<option value='+arr[0]+'>'+arr[1]+'</option>');
            }            
        });  
    }    
}



function assignRole() {        
//       $('#selectLeft option').attr('selected', 'selected');
//       $('#selectRight option').attr('selected', 'selected');
       var seluser = $('#seluser').val();
       if (seluser !="") {
           $('#selectRight option').prop('selected', true);
           $('#selectLeft option').prop('selected', true);
                 var multiplevalues = $('#selectRight').val();
                 var multiplevalues2 = $('#selectLeft').val();
//alert(JSON.stringify(multiplevalues2));
//alert(JSON.stringify(multiplevalues));
//                 alert(multiplevalues);
                 var form_id = $('#form_id').val();
               window.location.href="formpost/assignRoleToUser.php?user_id="+seluser+"&to_enable_roles="+multiplevalues+"&to_disable_roles="+multiplevalues2+"&form_id="+form_id;  
               
       } else {
           alert("Please select a user to assign roles");
       }
}
    
    
    

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            
            $menuitem = "assign_user_role";
//            include "sidemenu.".$this->currStore->usertype.".php";
            include "sidemenu.php";   
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();

?>

<div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Assign Role to User</h2>
                        <div class="common-content-block">
                            <!--<form  role="form" id="createuser" name="createuser" enctype="multipart/form-data" method="post" action="formpost/create_user.php">-->
                           <form  id="userRoleForm" name="userRoleForm" method="" onsubmit="assignRole(); return false;" > <!--action="formpost/assignFuncsToLocation.php"-->
                            <input type = "hidden" name="form_id" id="form_id" value="userRoleForm">
                             <div class="box box-primary"><br>
                                 <div class="col-md-12">
                                     <div class="col-md-6">
                                        <select id="seluser" name="seluser" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getBySelectUser(this.value);">
                                         <?php $users = $dbl->getAllActiveUsers();
                                         if(!empty($users)){
                                             ?>
                                             <option value="">Select User</option>
                                         <?php
                                             foreach($users as $user){ 
                                                 if(isset($user) && !empty($user) && $user != null){
                                         ?>
                                         <option value="<?php echo $user->id;?>"><?php echo $user->name;?></option>
                                                 <?php     } }
                                         }
                         ?>

                                     </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12" id="crlist">
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
                                 </div>
                                 <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary" value="Submit">
                                        </div>
                                    </div>
                                 </div>
                                
                             </div>   
                            </form>

                        </div>
                    </div>
                </div>
            </div>
                <div class="col-md-6">
                                                <?php if ($formResult->form_id == 'userRoleForm') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                </div>
        </div> 
 </div>
        
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


