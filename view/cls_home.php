<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once 'lib/locations/clsLocation.php';

class cls_home extends cls_renderer {
    function __construct($params = null) {
        $currStore = getCurrStore();
        if (isset($currStore)) {
            if($currStore->usertype == UserType::RFCManager){
                header("Location: " . DEF_SITEURL . "rfc");
            }else if ($currStore->usertype == UserType::RFCLogin){
               header("Location: " . DEF_SITEURL . "stores");
            }else if ($currStore->usertype == UserType::RFC || $currStore->usertype == UserType::Director || $currStore->usertype == UserType::PurchaseOfficer){
               header("Location: " . DEF_SITEURL . "stocktransfer");
            }else{
                header("Location: " . DEF_SITEURL . "products");
            }
        }
    }
    public function pageContent() {
        $currStore = getCurrStore();
        if (!isset($currStore)) {
            include_once 'inc.storehome.php';
        }
    }
}
?>
