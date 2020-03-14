<?php
$menu = array(
    "Masters" => array(
        "products" => array("Products", "products"),
        "prodprice" => array("Product Pricing","product/pricing"),
        "Transport" => array("Transports", "transports"),
         "rfc" => array("RFC ", "rfc"),
    ),
    "Transactions" => array(
       "CR sales Details" => array("CR Sales", "cr/sales/details"),
        "stocktransfer" => array("Stock Transfer","stocktransfer"),
        "CR stock Details" => array("CR stocks", "cr/itemstock/details"),
        "challanin" => array("Challans", "challans/in"),
    ),
    "Reports" => array(
        "dcstockreport" => array("Stock Report (DC)","stock/report"),
        "crstockreport" => array("Stock Report (CR)","cr/stock/report"),
        "crsalesreport" => array("Sales Report (CR)","cr/sales/report"),
        "stcreport" => array("STC Report","stc/report"),
        "storeport" => array("STO Report","sto/report"),
        "imprestreport" => array("Imprest Report (CR)","imprest/report"),
        "depositdetailsreport" => array("Deposit Details Report","deposit/details/report"),
    ),
    "Manage Settings" => array(
        "settings" => array("Change Password", "user/settings")      
    )
);
?>

<aside class="main-sidebar">
 
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
