<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once 'lib/db/DBLogic.php';

$currStore = getCurrStore();
// $crid = $currStore->crid;
$aColumns = array('challan_no','fromloc','toloc','vehicle_no','eway_bill','total_qty','total_value','pull_date');
$sColumns = array('challan_no','vehicle_no','eway_bill','pull_date');


$db = new DBConn();
$dbl = new DBLogic();
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;
$pid = isset($_GET['pid']) ? $_GET['pid'] : false;


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
$sOrder = " order by c.pulleddate desc ";
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
 * SQL queriespageLength: 10
 * Get data to display
 */

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}

$addClause= "";


//$status = 3; //pulled stock challans only
 $awaitingChallan = StockTransferChallanStatus::AwaitingIn;
 $completeChallan = StockTransferChallanStatus::Completed;
$sWhere .= "st.id = c.st_id and c.status in ($awaitingChallan,$completeChallan)";
$sQuery = "select SQL_CALC_FOUND_ROWS c.challan_no, c.st_id, c.vehicle_no, c.eway_bill, c.total_qty, c.total_value, c.pulleddate from st_challan c, it_stock_transfer st 
        $sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
//print_r($objs);
//return;
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      
        $stoDetails = $dbl->getStockTransferInfo($obj->st_id);

	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'challan_no') {
                $row[] = $obj->challan_no;
            }else if($aColumns[$i] == 'fromloc'){
                $row[] = $stoDetails->fromloc;
            }else if($aColumns[$i] == 'toloc'){
                $row[] = $stoDetails->toloc;
            }else if($aColumns[$i] == 'vehicle_no'){
                $row[] = $obj->vehicle_no;
            }else if($aColumns[$i] == 'eway_bill'){
                $row[] = $obj->eway_bill;
            }else if($aColumns[$i] == 'total_qty'){
                $row[] = $obj->total_qty;
            }else if($aColumns[$i] == 'total_value'){
                $row[] = $obj->total_value;
            }else if($aColumns[$i] == 'pull_date'){
                $row[] = $obj->pulleddate;
            }
             else $row[] ='-';
          
	}
	$rows[] = $row;
	$iTotal++;
}

$db->closeConnection();
/*
 * Output
 */
$output = array(
	
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
