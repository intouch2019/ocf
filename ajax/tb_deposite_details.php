<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$userid = $currStore->id;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('id','dispname', 'receipt_no', 'amount','chargetypedesc','description','by_user','ctime');
$sColumns = array('id','dispname', 'receipt_no', 'amount','chargetypedesc','description','by_user');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dbl = new DBLogic();

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
$sOrder = " order by ctime desc ";
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

//$aColumns = array('invoiceno','cname','cphone','totqty','tottax','totvalue','action');
//$status = InvoiceStatus::Created;
//$tablename = $dbl->getSalesTableName($userid);
$CRobj = $dbl->getCRDetailsByUserId($userid);
$crid = $CRobj->id;
$sWhere = "where u.id = d.by_user and r.id = d.crid and d.crid = $crid and e.id = d.payment_type";
$sQuery = " select SQL_CALC_FOUND_ROWS d.*, r.dispname, u.name as by_user, e.chargetypedesc from it_deposit_diary d, it_rfc_master r, it_users u, it_extra_charges e
	$sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nImprest Details query: ".$sQuery."\n",3,"tmp.txt");
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
        
//$aColumns = array('invoiceno','cname','cphone','totqty','tottax','totvalue','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }else if ($aColumns[$i] == 'dispname') {
                 $row[] = $obj->dispname;
             }else if ($aColumns[$i] == 'receipt_no') {
                 $row[] = $obj->receipt_no;
             }else if($aColumns[$i] == 'amount'){
                $row[] = $obj->amount;
             }else if ($aColumns[$i] == 'chargetypedesc') {
                 $new_Str = str_replace("charges", "", $obj->chargetypedesc);
                 $row[] = $new_Str;
             }else if ($aColumns[$i] == 'description') {
                 $row[] = $obj->description;
             }else if($aColumns[$i] == 'by_user'){
                $row[] = $obj->by_user;
             }else if($aColumns[$i] == 'ctime'){
                 $createDate = date('d-m-Y', strtotime($obj->ctime));
                $row[] = $createDate;
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
