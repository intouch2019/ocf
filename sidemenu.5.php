<?php
$menu = array(
        "TTK Item" => array(
                "ttkitem" => array("TTK Item Master List","items")
        ),
     "POINTS" => array(
//              "invpoints"  => array("Points By Invoices","tsh/invoice/points"),
         "invpoints" => array("Points By Invoices [Report]","invoice/points"),
//              "dealerpoints" => array("Points By Dealer","tsh/invoice/dealer")
         "invdealer" => array("Points By Dealers [Report]","invoice/dealer"),
         "schdealer" => array("Points By Scheme [Report]","scheme/dealer")
        ),
        "Distributor" => array(
//                "dist" => array("Distributor Overview","sman/dist"),
             "dist" => array("Distributor Overview", "dist"),
//                "distitems" => array("Distributor Items","sman/dist/items")
            "distitems" => array("Distributor Items", "dist/items")
	),
        "Return" => array(
           "salesRet" => array("Sales Return", "dist/sr")
        ),
        "Dealer" => array(        
        "dmap" => array("Dealer Mapping", "dealer/map")
        ),
        "Tie Up" => array(            
            "denroll" => array("Manage Dealer Enrolled","ttk/dealers/enrolled"),
            "reports" => array("Reports","tie/up/reports")
        ),
        "Reports" => array(
                "sales" => array("Sales Overview" ,"sales/overview"),
                "distStock" => array("Distributor Stock","dist/stock/report")
      
	),
        "Ordering" => array(
        "placeorder" => array("Place Order", "place/order"),
        "revieworder" => array("Review Order", "review/order")        
    ),
	"Manage" => array(
		"settings" => array("My Settings", "user/settings")
	)
);
?>
<div class="grid_2">
    <div id="section-menu">
        <ul class="section menu">
<?php foreach ($menu as $menuheading => $submenu) { ?>
            <li>
                <a class="menuitem"><?php echo $menuheading; ?></a>
                <ul class="submenu">
<?php foreach ($submenu as $menukey => $menudetail) {
	if ($menukey == $menuitem) { $selected = 'class="menuselect"'; } else { $selected = ""; }
?>
                    <li><a <?php echo $selected; ?> href="<?php echo $menudetail[1]; ?>"><?php echo $menudetail[0]; ?></a></li>
<?php } ?>
                </ul>
            </li>
<?php } ?>
        </ul>
    </div>
</div>
