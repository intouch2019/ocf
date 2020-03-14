<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once 'lib/locations/clsLocation.php';
require_once 'lib/core/Constants.php';

$error = array();
$clsLocation = new clsLocation();
extract($_POST);
$_SESSION['form_id'] = $form_id;
$_SESSION['form_post'] = $_POST;
$weekdays_arr = array();
$wflag="";
if($selltype==1){ //if location type is event
    $wflag=1;
}
//print_r($_POST);
//print "<br> WEEKDAY VAL: ".$weekday[1]['txtbox'];
//exit;
//
foreach ($weekday as $weekday_key => $arrs){
    $chkbox_val = "";$txtbox_val = "";
    foreach($arrs as $akey => $value){    
//        print "<br> KEY: $weekday_key:<br> ";
//        print_r($arr);
//        print "<br>";
       // print "<br> ARR $key :<br> KEY: $akey : VALUE : $arr <br>";
        //chkbox
        //txtbox
        if(trim($akey) == "chkbox"){
            //check if time is entered
            $chkbox_val = $value;
        }
        
        if(trim($akey) == "txtbox"){
            //check if time is entered
            $txtbox_val = $value;
        }
       
    }
    
    if(trim($chkbox_val)!="" && trim($chkbox_val)=="on"){
        if(trim($txtbox_val)==""){
            $wflag=1;
            $error[] = "Please enter week day ".Weekdays::getName($weekday_key)."'s time ";
            $_SESSION[$weekday_key."_error"]=1;
        }else{
            $_SESSION[$weekday_key."_txtboxval"] = $txtbox_val;
           // unset($_SESSION[$weekday_key."_error"]);
            
            //put values in weekdays array
            $weekdays_arr[$weekday_key] = $txtbox_val;
        }
    }
}

//print_r($error);
//exit;
$name = $_POST['name'];
$address = $_POST['address'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];
$selltype = $_POST['selltype'];
//$success = "$name location is created successfully";
$success = "";
$user = getCurrStore();



try{
    if(trim($name)=="" && trim($address)=="" && trim($city)=="" && trim($pincode)==""){
        $error['missing_name_addr'] = "Enter location name, address, city and pincode";
    }
    //else 
    if(trim($name)==""){
        $error['missing_name'] = "Enter location name";
    }
    //else 
    if(trim($address)==""){
        $error['missing_addr'] = "Enter location address";
    }
    //else
    if(trim($city)==""){
        $error['missing_city'] = "Enter city";
    }
    //else 
    if(trim($pincode)==""){
        $error['missing_pincode'] = "Enter location pincode";
    }
    //else 
    if(!is_numeric($pincode)){        
        $error['missing_pincode'] = "Enter valid pincode";
    }
    //else
    if(trim($selltype)==""){        
        $error['missing_ltype'] = "Please select location type";
    }
    //else 
    if(trim($lcode)==""){        
        $error['missing_ltype'] = "Please enter location code";
    }else{
        //get location by code 
        $cobj = $clsLocation->getLocationByCode($lcode);
        if(isset($cobj) && !empty($cobj)){
            $error['already_exist'] = "Location code '$lcode' already exist. Please enter another location code";
        }
    }
        if(trim($pincode)!=""){

            $len = strlen($pincode);
            if($len > 10){            
                $error['missing_pincode'] = "Pincode cannot be more than 10 digits.";
            }


        }
        
        //for dependant locations
        if(isset($is_dependant) && trim($is_dependant)=="on"){
            //check id dependant hub is selected or not
            if(! isset($selhub) || trim($selhub)==""){
                $_SESSION['d_hub_err'] = 1;
                $error['derr'] = "Please select dependant hub";
            }else{
                $_SESSION['d_hub_err'] = 1;
                $_SESSION['d_hub_val'] = $selhub;
                //unset($_SESSION['d_hub_err']);
            }
        }
        
        
        
        if(count($error) == 0){
            $obj = $clsLocation->getLocationByname($name);
            if(isset($obj) && !empty($obj)){
                $error['already_exist'] = "$name already exist. Try to create new location";
            }else{
                $last_inserted_id = $clsLocation->addLocation($name,$address,$city,$pincode,$user->id,$user->location_id,$selltype,$lcode,$is_dependant);
                if(trim($last_inserted_id)<=0){
                    $error['insert_fail'] = "New location is not created. Try to create it again.";
                }
                
//                print "LAST INSERTED ID: ".$last_inserted_id;
                if(trim($last_inserted_id) > 0){
                    //for dependant locations insertion
                    if(isset($is_dependant) && trim($is_dependant)=="on"){
                        //check id dependant hub is selected or not
                        if(isset($selhub) || trim($selhub)!=""){
                            $inserted_id = $clsLocation->insertLocationDependancy($selhub,$last_inserted_id,$user->id,$user->location_id);
                            if(trim($inserted_id) <= 0){
                              $error[] = "Dependancy could not be set. Please try again via edit feature. "; 
                            }
                        }
                    }
                }else{
                    $error[] = "Dependancy could not be set. Please try again via edit feature. ";
                }
                
                if(trim($last_inserted_id) > 0){
                    //put events info details
                    if(!empty($weekdays_arr)){
                        foreach($weekdays_arr as $day_of_week => $wtime){
                            $clsLocation->insertEventInfo($last_inserted_id,$day_of_week,$wtime,$user->id,$user->location_id);
                        }
                    }
                }
            }
        }
    //}
} catch (Exception $ex) {
    $error['exc'] = $ex->message;
}
//print_r($error);
if (count($error) > 0) {
    unset($_SESSION['form_errors']);
    unset($_SESSION['fpath']);    
    $_SESSION['form_errors'] = $error;
    $addClause="";
    if(trim($wflag)!=""){ $addClause .= "/wflag=$wflag";}
    $redirect = 'create/location'.$addClause;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    if($selltype != "1" && $selltype != "3"){ 
        //if location type is not event & online
        //redirect to assign functionalities page
      $redirect = 'func/location/lid='.$last_inserted_id;
    }else{
     $redirect = 'locations';
    }
    
    $allweekdays = Weekdays::getAll();
    foreach($allweekdays as $weekday_key => $value){
       unset($_SESSION[$weekday_key."_txtboxval"]); 
        unset($_SESSION[$weekday_key."_error"]);
    }
    unset($_SESSION['d_hub_err']);
    unset($_SESSION['d_hub_val']);
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;