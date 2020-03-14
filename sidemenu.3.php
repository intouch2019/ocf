<?php
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once 'session_check.php';
require_once 'lib/core/strutil.php';

$dbl = new DBLogic();
$userid = getCurrStoreId();
$stockpullAwaiting = $dbl->getcountstockpull($userid,StockTransferStatus::AwaitingIn);
$currStore = getCurrStore();
$crid = $currStore->crid;
$result = $dbl->checkIsImprestAvailable($crid);
if(isset($result)){
    $imprest = true;
}else{
    $imprest  = false;
}
$menu = array(
    "Masters" => array(
        "products" => array("Products", "products"),
        "prodprice" => array("Product Pricing","product/pricing"),
    ),
    "Transactions" => array(
        "sales" => array("Sales", "sales"),
        "challanin" => array("Challan pull", "challans/in"),
        "creditnote" => array("Credit Note", "creditnote"),
        "deposit_details" => array("Deposit Details", "deposit/details")
    )+($imprest ? array('imprest_register' => array("Imprest Register", "imprest/register")) : array()),
    "Reports" => array(
        "cpreport" => array("Collection Payment Report","collection/pay/report"),
        "crstockreport" => array("Stock Report (CR)","cr/stock/report"),
        "crsalesreport" => array("Sales Report (CR)","cr/sales/report"),
        "depositdetailsreport" => array("Deposit Details Report","deposit/details/report"),
    )+($imprest ? array('imprestreport' => array("Imprest Report (CR)", "imprest/report")) : array()),
    "Manage Settings" => array(
        "settings" => array("Change Password", "user/settings")      
    )
);
// $menu['Dashboard'] = array(
//     "dash" => array("Reports", DEF_SITEURL."DashbordReport/AdminLTE-2.1.1/pages/sales/sales.php")
// );
?>

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
     <?php foreach ($menu as $menuheading => $submenu) { ?>
     <li class="treeview">
      <a href="#">
        <i class="fa fa-dashboard"></i> <span><?php echo $menuheading; ?></span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
       <?php
       foreach ($submenu as $menukey => $menudetail) {
        if ($menukey == $menuitem) {
          $selected = 'class="active"';
        } else {
          $selected = "";
        }
        if($menukey == "dash"){
          ?>
          <li <?php echo $selected; ?> ><a href="<?php echo $menudetail[1]; ?>" target="_blank"><i class="fa fa-circle-o"></i> <?php echo $menudetail[0]; ?></a></li>
          <?php
        }else{
         ?>
         <li <?php echo $selected; ?> ><a href="<?php echo $menudetail[1]; ?>"><i class="fa fa-circle-o"></i> <?php echo $menudetail[0]; ?></a></li>
         <?php 
       }
     } ?>
   </ul>
 </li>
 <?php } ?>
</ul>
</section>
<!-- /.sidebar -->
</aside>
  <section id="page-content-wrapper" class="main-content">
  <!--<div class="sub-navigation">-->
<!--                    <ul>
                        <li>PO Tracking</li>                        
                    </ul>-->
                <!--</div>-->
