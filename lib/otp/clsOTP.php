<?php
require_once "lib/db/dbobject.php";
class clsOTP extends dbobject {
    public function __construct($commit=true) {
        parent::__construct($commit);
    }
    public function genOTP(){
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        for ($i = 0; $i < 5; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
    
    public function findOTPLoyalty($otployalty){ 
        $otployalty=$this->safe(trim($otployalty));
        $obj = $this->fetchObject("select * from it_loyalty_otp where otployalty=$otployalty and isactive = 1 and iscancelled = 0 and isused = 0 ");
        if ($obj) { return 1; }
        else { return "0"; }
    }
    
    public function findOTPRedeem($otpredeem){  
        $otpredeem=$this->safe($otpredeem);
        $obj = $this->fetchObject("select * from it_points where otpredeem=$otpredeem");
        if ($obj) { return 1; }
        else { return "0"; }
    }
    
    public function setOTPLoyalty($custid,$otployalty,$points_req,$amount){
        $otployalty=$this->safe(trim($otployalty));       
        $inserted = $this->execInsert("insert into it_loyalty_otp set userid = $custid , otp = $otployalty , points = $points_req , value = $amount , createtime = now()");
        if ($inserted>0) { return 1; }
        else { return "0"; }
    }
    
    public function createOTPRedeem($userid,$storeid,$otpredeem,$redeempoints){
        $otpredeem=$this->safe($otpredeem);
        $inserted = $this->execUpdate("insert into it_points set userid=$userid,storeid=$storeid,otpredeem=$otpredeem,redeempoints=$redeempoints");
        if ($inserted>0) { return 1; }
        else { return "0"; }
    }
    
    public function unusedOTPRedeem($userid,$storeid,$redeempoints){         
         $obj = $this->fetchObject("select * from it_points where userid = $userid  and storeid = $storeid and redeempoints = $redeempoints and bill_no is null and bill_amount is null and isvalid=1");
         if($obj){
             $str = $this->safe(trim("UNUSED_".$obj->otpredeem));
             $this->execUpdate("update it_points set isvalid=0 , otpredeem = $str where id = $obj->id");
         }
    }
    
    public function validOTP($userid,$otp){
        $otp = $this->safe(trim($otp));
        $obj = $this->fetchObject("select * from it_loyalty_otp where userid = $userid and otp = $otp ");
        if($obj){
            return 1;
        }else{
            return 0;
        }
    }
    
    public function validOTPAmount($userid,$otp,$amount){
        $otp = $this->safe(trim($otp));
        $obj = $this->fetchObject("select * from it_loyalty_otp where userid = $userid and otp = $otp and value = $amount");
        if($obj){
            return 1;
        }else{
            return 0;
        }
    }
    
    public function chkunused($userid,$otp,$amount){
        $otp = $this->safe(trim($otp));
        $obj = $this->fetchObject("select * from it_loyalty_otp where userid = $userid and otp = $otp and value = $amount and isused = 1");
        if($obj){
            return 1;
        }else{
            return 0;
        }
    }
    
    public function chkiscancelled($userid,$otp,$amount){
        $otp = $this->safe(trim($otp));
        $obj = $this->fetchObject("select * from it_loyalty_otp where userid = $userid and otp = $otp and value = $amount and iscancelled = 1");
        if($obj){
            return 1;
        }else{
            return 0;
        }
    }
    function __destruct() {
        parent::__destruct();
    }
}
