<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$hub_id = isset($_GET['hid']) ? ($_GET['hid']) : false;
if (!$hub_id) { return error("missing parameters"); }

try{
    $db = new DBConn();
    $str = "";
    $cnt = 0;
    $query1 = "select id, name from it_products order by name ";//limit 10
    $query2 = "select name from it_locations where location_type_id in(5,7)";
    // add is_active cls in below query
    $query3 = "select l.name from it_locations l, it_location_dependancy ld where l.id=ld.child_location_id and ld.parent_location_id = $hub_id";
//    print "$query1<br>";
//    print "$query2<br>";
//    print "$query3<br>";
    
    $pobjs = $db->fetchObjectArray($query1);
    $pwobjs = $db->fetchObjectArray($query2);
    $eobjs = $db->fetchObjectArray($query3);
    //create table heading
    $str .= "<thead><th>Product Name</th>";
    foreach ($pwobjs as $pwobj){
        $str .= "<th>$pwobj->name</th>";
        $cnt++;
    }
    foreach ($eobjs as $eobj){
        $str .= "<th>$eobj->name</th>";
        $cnt++;
    }
    $str .= "<th>Total</th></thead><tbody>";
    //create table body
    foreach($pobjs as $pobj){
        $tdcnt = $cnt;
        $str .= "<tr><td>$pobj->name</td> <input type = 'hidden' id ='$pobj->id' name ='$pobj->id'> "; 
        while($tdcnt > 0){
            $str .= "<td><input type='text' class = 'col-xs-6'></td>";
            $tdcnt--;
        }
        $str .= "<td><input type='text' class = 'col-xs-6'></td></tr>";
    }
    $str .= "</tbody>";
    echo json_encode($str);
    
}catch(Exception $xcp){
    print($xcp->getMessage());
}
