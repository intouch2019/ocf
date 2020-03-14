<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
$usertype = $currStore->usertype;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('grnno','supplier','pono','invno','invdate','grnqty','grnvalue','createdby','createdon','action');
$sColumns = array('g.grno', 's.name', 'p.pono', 'g.invoice_no', 'g.invoice_date' ,'g.tot_qty', 'g.tot_value','u.name','g.grndate');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

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
//$aColumns = array('id','name','state','address','graddress','contact_person','phone','email','gstno','panno','status','action');        
//echo "Status : ".$status."<br>";

$sWhere = " where p.id = g.poid and s.id = g.suppid and u.id = g.createdby and g.status = $status and g.inactive = 0 ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS g.id as grnid,g.grnno,s.company_name as supplier, p.pono as pono, g.invoice_no, g.invoice_date, g.tot_qty as qty, g.tot_value as value, u.name as createdby,g.grndate, g.createtime, g.inactive, g.uom_id
	from it_purchaseorder p, it_users u, it_suppliers s, it_grn g
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
//$aColumns = array('grnno','supplier','pono','invno','invdate','grnqty','grnvalue','createdby','createdon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'grnno') {
                 $row[] = $obj->grnno;
             }else if ($aColumns[$i] == 'supplier') {
                 $row[] = $obj->supplier;
             }else if ($aColumns[$i] == 'pono') {
                 $row[] = $obj->pono;
             }else if($aColumns[$i] == 'invno'){
                 $row[] = $obj->invoice_no;
             }else if($aColumns[$i] == 'invdate'){
                 $row[] = $obj->invoice_date;
             }else if($aColumns[$i] == 'grnqty'){
                 $row[] = $obj->qty;
             }else if($aColumns[$i] == 'grnvalue'){
                 $row[] = $obj->value;
             }else if($aColumns[$i] == 'createdby'){
                 $row[] = $obj->createdby;
             }else if($aColumns[$i] == 'createdon'){
                 $row[] = $obj->grndate;
             }else if($aColumns[$i] == 'action'){
                if($status == POStatus::Open){
                   $row[] = '<button class="btn btn-primary" type="button" onclick="editGRN(' . $obj->grnid . ','.$obj->uom_id.')">Edit</button>&nbsp;'
                           . '<button class="btn btn-primary" type="button" onclick="deleteGRN(' . $obj->grnid . ')">Delete</button>';            
                }else{
                    $row[] = "-";
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
