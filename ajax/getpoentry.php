<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$sel_subloc_id = isset($_GET['selid']) ? ($_GET['selid']) : false;
$loc_id = isset($_GET['loc_id']) ? ($_GET['loc_id']) : false;

if (!$sel_subloc_id) { return error("missing parameters"); }
if (!$loc_id) { return error("missing parameters"); }
$cnt=0;
try{
    $db = new DBConn();
    $str = $sel_subloc_id;
    $query1 = "select id, name from it_products order by name limit 10";//limit 10
//    $query2 = "select name from it_locations where location_type_id in(5,7)";
    // add is_active cls in below query
    $query3 = "select l.name from it_locations l, it_location_dependancy ld where l.id=ld.child_location_id and ld.parent_location_id = $sel_subloc_id";
//    print "$query1<br>";
//    print "$query2<br>";
    //print "$query3<br>";
    
    $pobjs = $db->fetchObjectArray($query1);
//    $pwobjs = $db->fetchObjectArray($query2);
    $eobjs = $db->fetchObjectArray($query3);
    //create table heading
    $str .= "<thead data-spy='affix'><th>Product Name</th>";
    $str .= "<th>Available Qty</th>";
//    foreach ($pwobjs as $pwobj){
//        $str .= "<th>$pwobj->name</th>";
//        $cnt++;
//    }
    foreach ($eobjs as $eobj){
        $str .= "<th>$eobj->name</th>";
        $cnt++;
    }
    $str .= "<th>Home Delivery</th><th>Buffer</th><th>Total</th></thead><br><tbody data-spy='scroll'>";
    //create table body
    foreach($pobjs as $pobj){
        $tdcnt = $cnt;
        $str .= "<tr><td class='filterable-cell'>$pobj->name</td> <input type = 'hidden' id ='$pobj->id' name ='$pobj->id'> "; 
        $str .= "<td class='filterable-cell'><input type='text' class = 'col-xs-8' value='0'></td>";
        while($tdcnt >= 0){
            $str .= "<td class='filterable-cell'><input type='text' class = 'col-xs-8'></td>";
            $tdcnt--;
        }
//        $str .= "<td class='filterable-cell'><input type='text' class = 'col-xs-8'></td>";
        $str .= "<td class='filterable-cell'><input type='text' class = 'col-xs-8'></td>";
        $str .= "<td class='filterable-cell'><input type='text' class = 'col-xs-8'></td></tr>";
    }
    $str .= "</tbody>";
    echo json_encode($str);
    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
