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

$aColumns = array('supplier','billno','billdate','gateentryno','pono','createdby','createdon','action');
$sColumns = array('s.company_name', 'sb.billno', 'sb.bill_date', 'sb.gaterntry_id','p.pono','u.name','sb.createtime');
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
$sOrder = " order by sb.createtime desc ";
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
//$aColumns = array('supplier','billno','billdate','gateentryno','pono','createdby','createdon','action');

$sWhere = " where sb.supplier_id = s.id and p.id = sb.po_id and u.id = sb.createdby";

if($status >= 0){
    $sWhere .= " and sb.status = ".$status;
}

$sQuery = "
	select SQL_CALC_FOUND_ROWS sb.id,s.company_name,sb.billno,sb.bill_date,sb.gateentry_id as gateentryno,p.pono,u.name as createdby,sb.status,sb.createtime 
	from it_users u, it_suppliers s, it_supplier_bill sb, it_purchaseorder p
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
//$aColumns = array('supplier','billno','billdate','gateentryno','pono','createdby','createdon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'supplier') {
                 $row[] = $obj->company_name;
             }else if ($aColumns[$i] == 'billno') {
                 $row[] = $obj->billno;
             }else if($aColumns[$i] == 'billdate'){
                 $row[] = ddmmyy($obj->bill_date);
             }else if($aColumns[$i] == 'gateentryno'){
                 $row[] = $obj->gateentryno;
             }else if($aColumns[$i] == 'pono'){
                 $row[] = $obj->pono;
             }else if($aColumns[$i] == 'createdby'){
                 $row[] = $obj->createdby;
             }else if($aColumns[$i] == 'createdon'){
                 $row[] = $obj->createtime;
             }else if($aColumns[$i] == 'action'){
                 if($obj->status == SupplierBillStatus::Open){
                    $row[] = '<button class="btn btn-primary" type="button" onclick="editSupplierBill(' . $obj->id . ')">Edit</button>&nbsp;'
                            . '<button class="btn btn-primary" type="button" onclick="deleteSupplierBill(' . $obj->id . ')">Delete</button>';            
                 }else if($obj->status == SupplierBillStatus::Submit){
                    $row[] = '<button class="btn btn-primary" type="button" onclick="viewSupplierBill(' . $obj->id . ')">View</button>';            
                 }else if($obj->status == SupplierBillStatus::Deleted){
                    $row[] = '<button class="btn btn-primary" type="button" onclick="viewSupplierBill(' . $obj->id . ')">View</button>';            
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
