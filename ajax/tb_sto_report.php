<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once 'lib/db/DBLogic.php';

$currStore = getCurrStore();
// $crid = $currStore->crid;
$aColumns = array('transferno','transferdate','fromloc','toloc','tot_qty');
$sColumns = array('transferno','transferdate');


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
$sOrder = " order by st.transferdate desc ";
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
//$transferdate = date('d-m-Y', strtotime(date($stoDetails->transferdate)));

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


$status = StockTransferStatus::Completed; 
$sWhere .= "st.status = $status";
$sQuery = "select SQL_CALC_FOUND_ROWS * from it_stock_transfer st 
        $sWhere 
	$sOrder
	$sLimit
";
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

//print_r($iFilteredTotal);


$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      

	$row = array();
        $stoDetails = $dbl->getStockTransferInfo($obj->id);
        
        $transferdate = date('d-m-Y', strtotime(date($stoDetails->transferdate)));

        
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'transferno') {
                //show in ddmmyy
                $row[] =  $stoDetails->transferno;
            }else if($aColumns[$i] == 'transferdate'){
                //show in ddmmyy
                $row[] =  $transferdate;
            }else if($aColumns[$i] == 'fromloc'){
                 $row[] =  $stoDetails->fromloc;

            }else if($aColumns[$i] == 'toloc'){
               $row[] = $stoDetails->toloc;
            }else if($aColumns[$i] == 'tot_qty'){
               $row[] = $obj->tot_qty;
            }
            else $row[] ='-';
	}
	$rows[] = $row;
	$iTotal++;
}
//print_r($iTotal);

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
