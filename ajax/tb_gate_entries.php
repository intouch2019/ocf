<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('gateentryno','supplier','transporter','lrno','qty','receivedby','receivedon','action');
$sColumns = array('g.gateentry_no', 's.name', 't.name', 'g.lrno','u.name','g.createtime');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dbl = new DBLogic();

$status = isset($_GET['status']) ? $_GET['status'] : false;

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
$sOrder = " order by g.createtime desc ";
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
//$aColumns = array('gateentryno','supplier','transporter','lrno','qty','receivedby','receivedon','action');

$sWhere = " where g.transport_id = t.id and s.id = g.supplier_id and u.id = g.received_by";
$sQuery = "
	select SQL_CALC_FOUND_ROWS g.id as id,s.company_name as supplier, t.name as transporter, g.lrno, g.qty_received as qty,u.name as receivedby, g.createtime as receivedon 
	from it_gateentry g, it_users u, it_suppliers s, it_transporters t
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
foreach ($objs as $obj){
        $tot_stk = 0;
	$row = array();
////$aColumns = array('gateentryno','supplier','transporter','lrno','qty','receivedby','receivedon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'gateentryno') {
                 $row[] = $obj->id;
             }else if ($aColumns[$i] == 'supplier') {
                 $row[] = $obj->supplier;
             }else if($aColumns[$i] == 'transporter'){
                 $row[] = $obj->transporter;
             }else if($aColumns[$i] == 'lrno'){
                 $row[] = $obj->lrno;
             }else if($aColumns[$i] == 'qty'){
                 $row[] = $obj->qty;
             }else if($aColumns[$i] == 'receivedby'){
                 $row[] = $obj->receivedby;
             }else if($aColumns[$i] == 'receivedon'){
                 $row[] = $obj->receivedon;
             }else if($aColumns[$i] == 'action'){
                 $obj_supp_bill = $dbl->getSupplierBillByGE($obj->id);
                 if($obj_supp_bill != NULL){
                    $row[] = "Supplier Bill No : ".$obj_supp_bill->billno;
                 }else{
                    $row[] = '<button class="btn btn-primary" type="button" onclick="editGateEntry(' . $obj->id . ')">Edit</button>&nbsp;'
                            . '<button class="btn btn-primary" type="button" onclick="billEntry(' . $obj->id . ')">Supplier Bill Entry</button>';            
                 }
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
