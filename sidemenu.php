<?php
require_once "../it_config.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'session_check.php';

$db = new DBConn();
$menu = array();
$currUser = getCurrStore();
$userId = $currUser->id;


try{   
    $query="select distinct menu, menuorder from it_permission p, it_role_permissions rp, it_user_roles ur where p.id = rp.permissionid and rp.roleid = ur.roleid and ur.userid = $userId order by menuorder asc";
//error_log("\nsidemenu qry:\n".$query,3,"ajax/tmp.txt");
$objs = $db->fetchObjectArray($query);
foreach($objs as $obj){
    $menuheading = $obj->menu;
    $obj->menu = array();
    $qry= "select p.* from it_permission p, it_role_permissions rp, it_user_roles ur where p.id = rp.permissionid and rp.roleid = ur.roleid and ur.userid = $userId and p.menu = '$menuheading' order by p.sub_menuorder asc ";
    
    $submenuobj = $db->fetchObjectArray($qry);
    foreach($submenuobj as $submenu){
        $obj->menu[$submenu->pagecode] = array($submenu->pagename,$submenu->pageuri);
    }
    $menu[$menuheading]=$obj->menu;
    
}
    
    
}catch(Exception $xcp){
    print $xcp->getMessage();
}
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
