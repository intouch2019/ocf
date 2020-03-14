<?php
$menu = array(
//    "View" => array(
//        "dashboard" => array("Dash Board", "dashboard")        
//    ),    
    "Manage" => array(
        //"region" => array("Accounts", "regions"),
        "stores" => array("Campus", "stores"),
    ),
    "Instances" => array(
        "pinst" => arraY("Campus Systems", "pos/instances"),
//        "tinst" => arraY("Tagging Instances","tagging/instances")
    ),
    "Users" => array(
        "users" => array("Users", "users")
    ),
//    " Manage Supplier" => array(
//        "msupp" => array("Supplier", "supplier"),
//        //"msuppro" => array("Supplier Product", "supplier/product"),
//    ),
//    "Check Out" => array(
//        "sorders" => array("Search Check Out", "search/orders"),
//        "cust" => array("Student Master", "customers"),
//    ),
    "Item Purchase" => array(
        "puorders" => array("Item Purchase", "purchase/orders"),
    ),
    "Reports" => array(
        "cstock" => array("Current Stock", "current/stock"),
        "tran" => array("Transaction", "transaction"),
        //"dsales" => array("Transactions Summary", "tran/summary"),
       // "fb" => array("Feedback", "feedback")
    ),
//    "Transaction" => array(
//        "tran" => array("Transaction", "transaction"),
//        //"ttypes" => array("Transaction Types", "transaction/types"),
//    ),
    "Catalog" => array(
        "categories" => array("Categories", "categories"),
        "masterProd" => array("Master Products", "master/products"),
       // "storeProd" => array("Store Products", "store/products")
    ),
//    "Selectives" => array(
//        "dtype" => array("Deleivery Types","dtypes"),        
//        "ptype" => array("Pickup Types","ptypes"),
//        "cparams" => array("Category Params","cparams"),
//    ),
//    "AddOns" => array(
//        "addons" => array("Master Addons","addons"),
//        "raddons" => array("Region Addons","region/addons")
//    ),
//     "Packages" => array(
//        "package" => array("Package","package"),
////        "pprod" => array("Package Product")
//    ),
//     "Taxes" => array(
//         "taxes" => array("Taxes", "taxes")
//     ),
    // "Payment Types" => array(
    //     "ptypes" => array("Payment Types", "payment/types")
    // ),
//     "Damage Types" => array(
//        "dtypes" => array("Damage Types","damage/types")
//    ),
//    "Wallet" => array(
//        "wallet" => array("Wallet","wallet")
//    ),
//    "Loyalty" => array(
//        "loyalty" => array("Loyalty","loyalty")
//    ),
//    "Tickets" => array(
//        "ticket" => array("Ticket", "region")
//    ),
//    "Orders" => array(
//        "active" => array("Active Orders", "active/order"),
//        "pending" => array("Pending Orders", "pending/order"),
//        "Complete" => array("Complete Orders", "complete/order"),
//    ),
//    
    "Manage Settings" => array(
        // "users" => array("Users", "user"),
        //"userperm" => array ("User Permissions","user/permissions"),
        "settings" => array("My Settings", "user/settings")      
    )
);
// $menu['Dashboard'] = array(
//     "dash" => array("Reports", DEF_SITEURL."DashbordReport/AdminLTE-2.1.1/pages/sales/sales.php")
// );
?>
<div class="grid_2" style="height:90vh; background:#686868;">
    <div id="section-menu" >
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
                            }  if($menukey == "dash"){
                            ?>
                            <li><a <?php echo $selected; ?> href="<?php echo $menudetail[1]; ?>" target="_blank"><?php echo $menudetail[0]; ?></a></li>
                            <?php
                            }else{
                            ?>
                            <li><a <?php echo $selected; ?> href="<?php echo $menudetail[1]; ?>"><?php echo $menudetail[0]; ?></a></li>
                            
                            <?php 
                            }
                            } ?>
                    </ul>
                </li>
            <?php } ?>
<!--                                <li>
                    <ul class="submenu"><li style="height:80vh; background:#686868;"><a>&nbsp;</a></li></ul>
                </li>-->
        </ul>
    </div>
</div>
