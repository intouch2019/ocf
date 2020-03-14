<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
$usertype = $currStore->usertype;
$userid = $currStore->id;
$crid = $currStore->crid;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('transferno','fromloc','toloc','qty','value','createdby','createdon','action');
$sColumns = array('s.transferno', 'd.dc_name', 's.to_location_id', 's.tot_qty', 's.tot_value','u.name','s.createtime');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

$status = isset($_GET['status']) ? $_GET['status'] : false;
//error_log("\nMSL Status query: ".$status."\n",3,"tmp.txt");
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
$sOrder = " order by s.createtime desc ";
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

//$sWhere = " where s.from_location_id = d.id and u.id = s.createdby and s.status = $status and s.inactive = 0 ";
$sWhere = " where case when s.from_location_type = 1 then s.from_location_id = d.id else s.from_location_id = c.id end and case when s.to_location_type = 1 "
        . "then s.to_location_id = d.id else s.to_location_id = c.id end and u.id = s.createdby and s.status = $status and s.inactive = 0 and c.id = $crid";
//$sQuery = "
//	select SQL_CALC_FOUND_ROWS s.id,s.transferno,d.dc_name,s.to_location_id,s.tot_qty,s.tot_value,u.name,s.createtime from it_stock_transfer s,it_dc_master d,it_users u
//	$sWhere 
//	$sOrder
//	$sLimit
//";
$sQuery = "
	select SQL_CALC_FOUND_ROWS s.id,s.transferno,s.from_location_type, case when s.from_location_type = 1 then d.dc_name else c.crcode end as fromloc,
        case when s.from_location_type = 1 then upper(c.crcode) else d.dc_name end as toloc,s.tot_qty,s.tot_value,u.name,s.transferdate,s.createtime from it_stock_transfer s,
        it_dc_master d,it_users u,it_rfc_master c
	$sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
// error_log("\nMSLmainnn query: ".$sQuery."\n",3,"tmp.txt");
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
//$aColumns = array('transferno','fromloc','toloc','qty','value','createdby','createdon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'transferno') {
                 $row[] = $obj->transferno;
             }else if ($aColumns[$i] == 'fromloc') {
                 $row[] = $obj->fromloc;
             }else if ($aColumns[$i] == 'toloc') {
                 $row[] = $obj->toloc;
             }else if($aColumns[$i] == 'qty'){
                 $row[] = $obj->tot_qty;
             }else if($aColumns[$i] == 'value'){
                 $row[] = $obj->tot_value;
             }else if($aColumns[$i] == 'createdby'){
                 $row[] = $obj->name;
             }else if($aColumns[$i] == 'createdon'){
                 $row[] = $obj->transferdate;
             }else if($aColumns[$i] == 'action'){
                if($status == StockTransferStatus::AwaitingIn){
                   $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->id . ')">Pull</button>';
//                           . '<button class="btn btn-primary" type="button" onclick="deleteGRN(' . $obj->id . ')">Delete</button>';            
                }else if($status == StockTransferStatus::Completed){
                   $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->id . ')">View</button>';
//                           . '<button class="btn btn-primary" type="button" onclick="deleteGRN(' . $obj->id . ')">Delete</button>';            
                }
                else{
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
