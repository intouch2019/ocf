<?php
$menu = array(
    "Masters" => array(
        "products" => array("Products", "products"),
        "suppliers" => array("Suppliers", "suppliers"),
        "Transport" => array("Transport", "transports"),
    ),  
    "Transactions" => array(
        "po" => array("Purchase Order","po"),
        "grn" => array("GRN","grn"),
        "stocktransfer" => array("Stock Transfer","stocktransfer"),
        "approveprodstock" => array("Approve Product Stock","product/stock/approve"),
        "challanin" => array("Challans", "challans/in"),
    ),
    "Reports" => array(
        "purchaseorder" => array("Purchase Order report","po/report"),
        "grnreports" => array("GRN Report","grn/report"),
        "crstockreport" => array("Stock Report (CR)","cr/stock/report"),
        "crsalesreport" => array("Sales Report (CR)","cr/sales/report"),
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
