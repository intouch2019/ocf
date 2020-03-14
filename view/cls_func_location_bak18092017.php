<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once 'lib/locations/clsLocation.php';

class cls_func_location extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
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



function getLocationSelect(loc_id){
        var ajaxUrl = "ajax/getLocationAssgnFuncPgs.php?location_id="+loc_id;
//        alert(ajaxUrl);        
        $.getJSON(ajaxUrl, function(data) {
//            alert(data);
            //var options = $('#selectLeft').attr('options');
           //  $('#selectLeft option').attr('selected', 'selected');
            //options.length = 1;
//                    options.length = 1;
//alert(data.length);
            $("#selectLeft").empty();
//            console.log(data);
//            console.log(data.length);
//            alert(data.length);
            
            for (var i = 0; i < data.length; i++) {
              //  console.log(data[i]);
                var arr = data[i].split('::');
               // options[options.length] = new Option(arr[2] , arr[0], false, false);
               $("#selectLeft").append('<option value='+arr[0]+'>'+arr[2]+'</option>');
            }            
        }); 
        
        var ajaxPUrl = "ajax/getNotAssgndLocFuncPgs.php?location_id="+loc_id;
//        alert(ajaxPUrl);        
        $.getJSON(ajaxPUrl, function(data) {
//            var options = $('#selectRight').attr('options');           
//            options.length = 1;
//            console.log(data);
//            console.log(data.length);
           //alert(data);
           $("#selectRight").empty();
            for (var i = 0; i < data.length; i++) {
               // console.log(data[i]);
                var arr = data[i].split('::');
                //options[options.length] = new Option(arr[2] , arr[0], false, false);
                $("#selectRight").append('<option value='+arr[0]+'>'+arr[2]+'</option>');
            }            
        });  
}

function assignPg() {
        //alert("here");
       $('#selectLeft option').attr('selected', 'selected');
       $('#selectRight option').attr('selected', 'selected');
       var sellocation = $('#sellocation').val();
//       alert(sellocation);
       //alert(storeid);commented
       if (sellocation !="") {
//           if (reporttype=="itemwise") {
                var multiplevalues = $('#selectLeft').val();
//                alert(multiplevalues);
                var multiplevalues2 = $('#selectRight').val();
//                alert(multiplevalues2);
//                alert("formpost/assignFuncsToLocation.php?location_id="+sellocation+"&to_enable_pgs="+multiplevalues+"&to_disable_pgs="+multiplevalues2);
                window.location.href="formpost/assignFuncsToLocation.php?location_id="+sellocation+"&to_enable_pgs="+multiplevalues+"&to_disable_pgs="+multiplevalues2;                           
       } else {
           alert("please select a user to assign pages");
       }
}
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $formResult = $this->getFormResult();
            $menuitem = "func_to_loc";//pagecode
            include "sidemenu.php";    
            $clsLocation = new clsLocation();
//                        include "sidemenu.".$this->currStore->usertype.".php";    
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>
<div class="container-section">
    <div class="row">        
        <div class="col-md-12">
            <div class="content-block table-task">
               <h2 class="title-bar">Assign Functionality to location</h2> 
               <div class="common-content-block"> 
                   <div class="box box-primary"><br>
                      <form  id="locFuncForm" name="locFuncForm" method="" onsubmit="assignPg(); return false;" > <!--action="formpost/assignFuncsToLocation.php"-->
                       <div class="box-body">
                           <div class="form-group">
                                <select id="sellocation" name="sellocation" class="chzn-select" style="width:25%" single onchange="getLocationSelect(this.value);">
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
                            <div class="form-group">
                                <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive nowrap" width="100%" border="0" colspan="4">                   
                                    <thead>
                                        <tr>
                                            <td colspan="2">Enabled Functionalities</td>
                                            <td colspan="1">&nbsp;</td>
                                            <td colspan="2">Disabled Functionalities</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td rowspan="3" colspan="2" align="right">                                        
                                            <select name="selectLeft"   multiple size="10" style="width:100%;"   id="selectLeft"> <!--style="width:200px;"-->
                                            </select>
                                        </td>
                                        <td colspan="1" rowspan="3"> <!--style="vertical-align:middle;"-->
                                            <button type="button" class="btn btn-primary" name="btnRight"  id="btnRight"  onClick="javaScript:moveToRightOrLeft(1);">&gt</button>
                                                <!--<input name="btnRight" type="button" id="btnRight" value="&gt;" onClick="javaScript:moveToRightOrLeft(1);">-->
                                            <br/><br/>
                                            <button type="button" class="btn btn-primary" name="btnLeft" type="button" id="btnLeft"  onClick="javaScript:moveToRightOrLeft(2);">&lt</button>
                                            <!--<input name="btnLeft" type="button" id="btnLeft" value="&lt;" onClick="javaScript:moveToRightOrLeft(2);">-->
                                            <br/><br/>
                                            <!--<input name="btnLeftToRight" type="button" id="btnLeftToRight" value="&gt;&gt;" onClick="javaScript:moveAllLeftToRight();">-->                        
                                            <button type="button" class="btn btn-primary" name="btnLeftToRight" type="button" id="btnLeftToRight" onClick="javaScript:moveAllLeftToRight();">&gt&gt</button>                        
                                            <br/><br/>
                                            <button type="button" class="btn btn-primary" name="btnRightToLeft" type="button" id="btnRightToLeft" onClick="javaScript:moveAllRightToLeft();">&lt&lt</button>
                                            <!--<input name="btnRightToLeft" type="button" id="btnRightToLeft" value="&lt;&lt;" onClick="javaScript:moveAllRightToLeft();">-->

                                        </td>
                                        <td rowspan="3" colspan="2" align="left">
                                            <select name="selectRight" multiple size="10" style="width:100%;" id="selectRight">   <!-- class="selectpicker" style="width:200px;"-->                                    
                                            </select>
                                        </td>                                                 
                                    </tr>                                  
                                    </tbody>
                                    <tfoot>
                                       <tr>
                                            <td>
                                                <input type="submit" class="btn btn-primary" value="Submit">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                  <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>                              
                                  </div> 
                                 </div>  
                               
                            </div>                       
                       </div>
                        
                       </form>  
                        
                   </div>
                </div>   
            </div>    
     </div>
 </div>    
</div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


