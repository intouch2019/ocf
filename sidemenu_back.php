<?php
require_once "../it_config.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once 'session_check.php';

$db = new DBConn();
$menu = array();
$currUser = getCurrStore();


try{   
//if($currUser->usertype == UserType::Dealer){$sClause = " order by menuhead,sequence desc ";}else{$sClause ="";}
//$query = "select distinct menuhead, sequence from it_pages where id in (select page_id from it_user_pages where user_id = $currUser->id ) and sequence > 0 $sClause";
$query=" select  distinct menuhead, sequence from it_functionality_pages p, it_location_functionalities l, it_user_location_functionalities u where u.location_functionality_id = l.id and l.functionality_id = p.id and u.user_id = $currUser->id and  u.location_id = $currUser->location_id and p.sequence > 0 and u.is_active = 1 and l.is_active = 1 "; // and p.sequence >0
//error_log("\nsidemenu qry:\n".$query,3,"ajax/tmp.txt");
$objs = $db->fetchObjectArray($query);
foreach($objs as $obj){
    $menuheading = $obj->menuhead;
    $obj->menuhead = array();
    //$qry = " select p.* from it_pages p , it_user_pages u where p.menuhead = '$menuheading' and p.sequence = $obj->sequence  and p.id = u.page_id and u.user_id = $currUser->id order by p.submenu_seq asc"; //and p.sequence > 0 ";
   $qry= "select p.* from it_functionality_pages p, it_location_functionalities l, it_user_location_functionalities u where  u.location_functionality_id = l.id and l.functionality_id = p.id and u.user_id = $currUser->id and  u.location_id = $currUser->location_id and p.sequence > 0 and p.sequence=$obj->sequence and p.menuhead= '$menuheading' and u.user_id= $currUser->id  and u.is_active = 1 and l.is_active = 1 order by p.submenu_seq asc";
//    echo "<br/>menuitem qry:".$qry."<br/>";
    //error_log("\nsubmenu qry:\n".$qry,3,"tmp.txt");
    $submenuobj = $db->fetchObjectArray($qry);
    foreach($submenuobj as $submenu){
//        if(isset($_SESSION['fpg']) && trim($_SESSION['fpg']) !=""){
//          $_SESSION['fpg'] =  $submenu->pageuri; 
//        }
        $obj->menuhead[$submenu->pagecode] = array($submenu->pagename,$submenu->pageuri);
    }
    $menu[$menuheading]=$obj->menuhead;
    
}    

//print_r($_SESSION);
//print_r($menu);
//    return $menu;
}catch(Exception $xcp){
    print $xcp->getMessage();
}
//print_r($menu);
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
