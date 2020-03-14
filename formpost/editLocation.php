<?php
require_once("../../it_config.php");
require_once "lib/db/DBConn.php";
require_once 'session_check.php';
require_once "lib/locations/clsLocation.php";
require_once 'lib/core/Constants.php';

$error = array();

extract($_POST);
//print_r($_POST);
$_SESSION['form_id'] = $form_id;
$lid = trim($_POST['loc_id']);
$lname= trim($_POST['locname']);
$address = trim($_POST['address']);
$city = trim($_POST['city']);
$pincode = trim($_POST['pincode']);
$isactive = trim($_POST['actvsel']);
$_SESSION['form_post'] = $_POST;
$weekdays_arr = array();

$success = "";
$db = new DBConn();
$clsloc = new clsLocation();
$user = getCurrStore();
$userid = $user->id;
try{
    $updatedrow =0;
    
    foreach ($weekday as $weekday_key => $arrs){
            $chkbox_val = "";$txtbox_val = "";
            foreach($arrs as $akey => $value){    
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
                    $error[] = "Selected week day ".Weekdays::getName($weekday_key)."'s time cannot be blank ";
                    $_SESSION[$weekday_key."_ed_error"]=1;
                }else{
                    $_SESSION[$weekday_key."_ed_txtboxval"] = $txtbox_val;
                   // unset($_SESSION[$weekday_key."_error"]);
                    //put values in weekdays array
                    $weekdays_arr[$weekday_key] = $txtbox_val;
                }
            }
    }
    
    //print_r($weekdays_arr);
    
    
    if(trim($lid)=="" && trim($lname)=="" && trim($address)=="" && trim($city)=="" && trim($pincode)=="" ){
        $error['missing_name_addr'] = "Name, Address, City, Pincode can't be Empty";
    }
    //else 
    if(trim($lname)==""){
        $error['missing_name'] = "Enter Location Name";
    }
    //else 
    if(trim($address)==""){
        $error['missing_address'] = "Enter Address";
    }
    //else
    if(trim($city)==""){
        $error['missing_city'] = "Enter City";
    }
    //else 
    if(trim($pincode)==""){
        $error['missing_pin'] = "Enter Pincode";
    }
    //else
    if(!is_numeric($pincode)){        
        $error['missing_pincode'] = "Enter valid pincode";
    }
    //else
    if(trim($loccode)==""){        
        $error['missing_pincode'] = "Enter location code";
    }else{
        //get location by code 
        $cobj = $clsloc->getLocationByCode($loccode,$lid);
//        print "<br>";
//        print_r($cobj);
        if(isset($cobj) && !empty($cobj)){
            $error['already_exist'] = "Location code '$loccode' already exist. Please enter another location code";
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
                $_SESSION['ed_hub_err'] = 1;
                $error['derr'] = "Please select dependant hub";
            }else{
                 $_SESSION['ed_hub_err'] = 1;
                $_SESSION['ed_hub_val'] = $selhub;
                //unset($_SESSION['ed_hub_err']);
            }
        }
        
        if(count($error) == 0){
            $pobj = $clsloc->getLocationByname($lname,$lid);
            if($pobj){
                 $error['already_exist'] = "$lname location already exist.";
            }else{
                //update product               
               $updatedrow= $clsloc->updateLocation($lid,$lname,$address,$city,$pincode,$userid,$isactive,$user->location_id,$loccode);
    //         echo $updatedrow;
               //for dependant locations insertion
                if(isset($is_dependant) && trim($is_dependant)=="on"){
                    //check id dependant hub is selected or not
                    if(isset($selhub) || trim($selhub)!=""){
                        //$inserted_id = $clsLocation->insertLocationDependancy($selhub,$last_inserted_id,$user->id,$user->location_id);
                        $flag=1;
                        $clsloc->updateLocationDependancyFlag($lid,$flag,$user->id,$user->location_id);
                        $clsloc->updateLocationDependancy($selhub,$lid,$user->id,$user->location_id);
                        
                    }
                }else{
//                    print "<br>IN ELSE <br>";
                    //check if location type is event
                    if(trim($loc_type_id) == "1"){
                        //update is_dependant = 0
                        $flag=0;
                        $clsloc->updateLocationDependancyFlag($lid,$flag,$user->id,$user->location_id);
                        //inactivate from dependancy table
                        $clsloc->inactivateLocationDependancy($lid,$user->id,$user->location_id);
                    }
                }
                
                
                    //put events info details
                if(!empty($weekdays_arr)){
                    //first inactive all event info
                    $clsloc->inactiveEventInfo($lid);
                    
                    foreach($weekdays_arr as $day_of_week => $wtime){
                        $eobj = $clsloc->fetchEventInfo($lid,$day_of_week);
                        if(isset($eobj) && !empty($eobj) && $eobj != null){
                            //update
                            $clsloc->updateEventInfo($lid,$day_of_week,$wtime,$user->id,$user->location_id);
                        }else{
                            //insert
                            $clsloc->insertEventInfo($lid,$day_of_week,$wtime,$user->id,$user->location_id);
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
    $redirect = 'location/edit/lid='.$lid;
} else {
    unset($_SESSION['form_success']);
    unset($_SESSION['fpath']);
    unset($_SESSION['form_id']);
    unset($_SESSION['form_post']);
    $_SESSION['form_success'] = $success;
    $redirect = 'locations';
    
    $allweekdays = Weekdays::getAll();
    foreach($allweekdays as $weekday_key => $value){
       unset($_SESSION[$weekday_key."_ed_txtboxval"]); 
        unset($_SESSION[$weekday_key."_ed_error"]);
    }
    unset($_SESSION['ed_hub_err']);
    unset($_SESSION['ed_hub_val']);
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;