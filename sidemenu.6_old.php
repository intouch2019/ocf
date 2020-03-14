<?php
require_once "lib/db/DBConn.php";
$db = new DBConn();
$qry = "select count(*) as cnt from it_ttk_items where itemname is null or material_id is null or category_id  is null  or hierarchy_id is null "; //or hierarchy_id is null
$ttkItem = $db->fetchObject($qry);
$menu = array(       
    "CRM" => array(
       "sorders" => array("Search Orders","search/orders"),
        "stickets" => array("Search Tickets","search/tickets"),
        "crm" => array("CRM","crm"),
        "cust" => array("Customers Master","customers"),
        "porders" => array("Package Orders","package/orders"),   
    ),   
//    "Manage Settings" => array(
//       // "users" => array("Users", "user"),
//        //"userperm" => array ("User Permissions","user/permissions"),
//        "settings" => array("My Settings", "user/settings")
//    )
);
?>
<div class="grid_2">
    <div id="section-menu">
        <ul class="section menu">
<?php foreach ($menu as $menuheading => $submenu) { ?>
                <li>
                    <a class="menuitem"><?php echo $menuheading; ?></a>
                    <ul class="submenu">
    <?php
    foreach ($submenu as $menukey => $menudetail) {
        if ($menukey == $menuitem) {
            $selected = 'class="menuselect"';
        } else {
            $selected = "";
        }
        ?>
                            <li><a <?php echo $selected; ?> href="<?php echo $menudetail[1]; ?>"><?php echo $menudetail[0]; ?></a></li>
                <?php } ?>
                    </ul>
                </li>
<?php } ?>
        </ul>
    </div>
</div>
