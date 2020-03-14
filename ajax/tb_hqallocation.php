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

$aColumns = array('location','hqno','allctdttm');
$sColumns = array('l.name','hq.hq_id','hq.allocation_dttm');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$lid = isset($_GET['locid']) ? $_GET['locid'] : false;
$alloctndt = isset($_GET['alloctndt']) ? $_GET['alloctndt'] : false;
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
$sOrder = " order by hq.updatetime desc ";
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
if(trim($lid)!="" && trim($lid)!="0" && $lid != null){
//    $lClause .= " and hqi.dispatch_location_id = $lid ";
    $lClause .= "  and hq.purchase_in_location_id = $lid ";
}
$dtClause= "";

if(trim($alloctndt)!=""  && trim($alloctndt)!="select" && trim($alloctndt)!="Select Date" && trim($alloctndt) != null){
//    $alloctndt= str_replace("-", "/", $alloctndt);
//    $dt = new DateTime($alloctndt);
//    $allotdate_db =  $db->safe(date_format($dt, 'Y-m-d H:i:s'));
    $dt = yymmdd($alloctndt);
    $allotdate_db = $db->safe($dt);
    $dtClause .= " and  hq.allocation_dttm = $allotdate_db ";
}
        //select l.name, p.name,qty,purpose from it_hq_allocation hq, it_hq_allocation_items hqi, it_locations l, it_products p where hq.id = hqi.hq_id and hqi.dispatch_location_id= l.id and hqi.product_id = p.id ;
$sWhere .= "hq.purchase_in_location_id = l.id $lClause $dtClause";
$sQuery = "select SQL_CALC_FOUND_ROWS l.name as locname,hq.id as id, hq.hq_no as hqno,date(hq.allocation_dttm) as allctdttm from it_hq_allocation hq, it_locations l
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
             if ($aColumns[$i] == 'location') {
                 $row[] = $obj->locname;
//             }else if($aColumns[$i] == 'product'){
//                 $row[] = $obj->prodname;
             }else if($aColumns[$i] == 'hqno'){
//                 $row[] = $obj->hqno;
                 $row[] = '<a onclick="showAllocationDetails(' . $obj->id . ')" href="javascript:void(0);"><u>'.$obj->hqno.'</u></a> ';
             }else if($aColumns[$i] == 'allctdttm'){
                 $row[] = ddmmyy($obj->allctdttm);
//             }else if($aColumns[$i] == 'quantity'){
//                  $row[] = $obj->quantity; 
//             }else if($aColumns[$i] == 'purpose'){
//                  $row[] = PurposeType::getName($obj->purpose); 
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
