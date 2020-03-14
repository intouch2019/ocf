<?php
$menu = array(
    "Masters" => array(
        "products" => array("Products", "products"),
        "users" => array("Users", "users"),
        "Transport" => array("Transport", "transports"),
    ),
    "Transactions" => array(
        "approveprodprice" => array("Approve Product Price","product/pricing/approve"), 
        "approveprodstock" => array("Approve Product Stock","product/stock/approve"), 
        "po" => array("Purchase Order","po")
/*        "poapprove" => array("Approve Purchase Order","po/awaiting/approvals"),
        "pocancel" => array("Cancel Purchase Order","po/awaiting/cancel"),*/
         
    ),
    "Reports" => array(
        "purchaseorder" => array("Purchase Order report","po/report"),
        "grnreports" => array("GRN Report","grn/report"),
        "dcstockreport" => array("Stock Report (DC)","stock/report"),
        "crstockreport" => array("Stock Report (CR)","cr/stock/report"),
        "crsalesreport" => array("Sales Report (CR)","cr/sales/report"),
        //"po" => array("Purchase Order","po")
/*        "poapprove" => array("Approve Purchase Order","po/awaiting/approvals"),
        "pocancel" => array("Cancel Purchase Order","po/awaiting/cancel"),*/
        
    ),
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