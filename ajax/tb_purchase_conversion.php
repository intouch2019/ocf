<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}


$aColumns = array('prodname','pqty','packsize','req_packets','act_packets','diff_packets','reason');
$sColumns = array('p.name','pi.qty_in_kg','ps.pack_size');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$pid = isset($_GET['purid']) ? $_GET['purid'] : false;
$purin_dt = isset($_GET['purdt']) ? $_GET['purdt'] : false;
/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by pi.updatetime desc ";
if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = " ORDER BY  ";
	for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
	{
		if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		{
			$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
			 	".$db->getConnection()->real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
	}
	
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == " ORDER BY " )
	{
		$sOrder = "";
	}
}


/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */

$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
	$sWhere = "WHERE (";
	for ( $i=0 ; $i<count($sColumns) ; $i++ )
	{
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string( $_GET['sSearch'] )."%' OR ";
	}
	$sWhere = substr_replace( $sWhere, "", -3 );
	$sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($sColumns) ; $i++ )
{
	if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && isset($_GET['sSearch_'.$i]) && $_GET['sSearch_'.$i] != '' )
	{
		if ( $sWhere == "" )
		{
			$sWhere = "WHERE ";
		}
		else
		{
			$sWhere .= " AND ";
		}
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string($_GET['sSearch_'.$i])."%' ";
	}
}

/*
 * SQL queries
 * Get data to display
 */
// select l.name,b.bin,p.name,s.qty from it_stock_current s ,it_locations l ,it_bins b ,it_products p  where l.id= s.createdat_locationid and b.id =s.bin_id and p.id= s.product_id;
if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
$lClause="";
if(trim($pid)!="" && trim($pid)!="0" && $pid != null){
    $lClause .= "  and pi.pur_in_id = $pid ";
}
$dtClause= "";
//select p.name as prodname, pi.qty_in_kg as pqty, ps.pack_size as packsize from it_purchase_in_items pi, it_products p, it_pack_size ps where pur_in_id =1 and p.id=pi.product_id and ps.id = p.pack_size_id;

if(trim($purin_dt)!=""  && trim($purin_dt)!="select" && trim($purin_dt)!="Select Date" && trim($purin_dt) != null){
    $dt = yymmdd($purin_dt);
    $purin_dt_db = $db->safe($dt);
    $dtClause .= " and  pin.purin_dt = $purin_dt_db ";
}
        //select l.name, p.name,qty,purpose from it_hq_allocation hq, it_hq_allocation_items hqi, it_locations l, it_products p where hq.id = hqi.hq_id and hqi.dispatch_location_id= l.id and hqi.product_id = p.id ;
$sWhere .= " p.id=pi.product_id and ps.id = p.pack_size_id and u.id = p.uom_id and pi.pur_in_id = pin.id $lClause $dtClause";
$sQuery = "select SQL_CALC_FOUND_ROWS p.id as prodid, p.name as prodname,pi.id as itmid, pi.qty_in_kg as pqty, ps.id as packsizeid, u.id as uomid, ps.pack_size as packsize
           from it_purchase_in_items pi, it_purchase_in pin, it_products p, it_pack_size ps, it_uom u
        $sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            $act_id = "act".$obj->itmid;
            $act_id_db = $db->safe($act_id);
            $diff_id = "diff".$obj->itmid; 
            $diff_id_db = $db->safe($diff_id);
            $reason_id = "reason".$obj->itmid; 
            $reason_id_db = $db->safe($reason_id);
            $prodid = "productid".$obj->itmid;
             if ($aColumns[$i] == 'prodname') {
                 $row[] = $obj->prodname;
             }else if($aColumns[$i] == 'pqty'){
                 $row[] = $obj->pqty;
             }else if($aColumns[$i] == 'packsize'){
                 $row[] = $obj->packsize; 
             }else if($aColumns[$i] == 'req_packets'){
                //calculate packets;
                 //check packsize
                $pc_size = $obj->packsize;
                $pc_size_arr = explode(" ",$pc_size);
//                        print_r($pc_size_arr);
                $psqty = $pc_size_arr[0];
                $unit = $pc_size_arr[1];
                
                if($obj->uomid == 2){
                    //product uom is gms           
                        $Q_kg= trim($psqty)/1000;
//                            print"\n kg=$Q_kg";
                        $tot_packet = trim($obj->pqty)/$Q_kg ;
//                            print"\n total packet = $tot_packet";
                }else if($obj->uomid == 1){
                    //else if umo is in kg
                    //product uom is kq           
                    if(trim($unit)==""|| trim($unit)=='gm' || trim($unit)=='gms' || trim($unit)=='grm' || trim($unit)=='grams'){
//                            print"\n convert";
                        $Q_kg= trim($psqty)/1000;
//                            print"\n kg=$Q_kg";
                        $tot_packet = trim($obj->pqty)/$Q_kg ;
//                            print"\n total packet = $tot_packet";
                    }else{
                        //packet = kg 
                        $tot_packet = trim($obj->pqty);
//                            print"\n not convert";
                    } 
                }else{
                    //else if umo is not gms
                    $tot_packet = trim($obj->pqty);
                }
                $row[] = $tot_packet;
             }else if($aColumns[$i] == 'act_packets'){
                $row[] = '<input type="text" id="'.$act_id.'" name="item['.trim($obj->itmid).'][act]" value="" onkeyup="calculate_diff('.$reason_id_db.','.$act_id_db.','.$diff_id_db.','.$tot_packet.',this.value);" style="width:25%">'
                        . '<input type ="hidden" id="'.$prodid.'" name ="item['.trim($obj->itmid).'][prodid]" value = "'.$obj->prodid.'">'
                        . '<input type ="hidden" id="'.$tot_packet.'" name ="item['.trim($obj->itmid).'][reqpackets]" value = "'.trim($tot_packet).'">'
                        . '<input type ="hidden" id="'.trim($obj->pqty).'" name ="item['.trim($obj->itmid).'][purchaseqty]" value = "'.trim($obj->pqty).'">'; //"'.$tot_packet.'",this.value
//                $row[] = '<input type="text" id="'.$act_id.'" name="'.$act_id.'" value="" onkeyup="calculate_diff('.$reason_id_db.','.$act_id_db.','.$diff_id_db.','.$tot_packet.',this.value);" style="width:25%">'
//                        . '<input type ="hidden" id="'.$prodid.'" name ="'.$prodid.'" value = "'.$obj->prodid.'">'; //"'.$tot_packet.'",this.value
             }else if($aColumns[$i] == 'diff_packets'){
                $row[] = '<input type="text" class ="required" id="'.$diff_id.'" name="item['.trim($obj->itmid).'][diff]" readonly style="width:25%">';
//                $row[] = '<input type="text" id="'.$diff_id.'" name="'.$diff_id.'" readonly style="width:25%">';
             }else if($aColumns[$i] == 'reason'){
                $row[] = '<input type="text" class ="required" id="'.$reason_id.'" name="item['.trim($obj->itmid).'][reason]" value="" >';
//                $row[] = '<input type="text" class ="required" id="'.$reason_id.'" name="'.$reason_id.'" value="" >';
             }else{
                $row[] = "-";
             }   
       
	}
	$rows[] = $row;
	$iTotal++;
}

$db->closeConnection();
/*
 * Output
 */
$output = array(
	//"sEcho" => intval($_GET['sEcho']),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
