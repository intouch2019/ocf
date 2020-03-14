<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

$city = isset($_GET['city']) ? $_GET['city'] : false;
$dtrng = isset($_GET['dtrng']) ? $_GET['dtrng'] : false;

$aColumns = array('city','hub','order_no','order_date','order_delivery_date','type','customer_name','status','qty','value');
$sColumns = array('h.name', 'o.order_no', 'o.order_date' , 'o.type' ,'o.customer_name' , 's.title' ,'o.total_qty' , 'o.total_value');

/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

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
$sOrder = "";
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
$addClause = "";
 if(trim($city)!=""){
     $addClause .= " and o.region_id = $city ";
 }
// print "$strdt : $enddt";
 if(trim($dtrng)!=""){
    $dtrng = str_replace(":","-",$dtrng);
    $dtparts = explode(" ",$dtrng);
    $strdt = yymmdd($dtparts[0]);
    $enddt = yymmdd($dtparts[2]);
    $addClause .= " and date(o.order_delivery_date) between '$strdt' and '$enddt' ";
 }

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
if($sOrder==""){
    $sOrder = " ORDER BY o.id DESC";
}
$sWhere .= " o.hub_id = h.id and s.id = o.status and o.type = ot.id and o.region_id = r.id and o.status != 28 $addClause ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS o.id , h.name as hub, o.order_no, o.order_date , ot.description as type ,o.customer_name as customer_name, s.title as status ,o.total_qty as qty, o.total_value as value, date(o.order_delivery_date) as order_delivery_date, r.title
	from orders o , hubs h, status s, order_types ot, regions r
	$sWhere
	$sOrder
	$sLimit
";
//$sWhere .= " o.hub_id = h.id and s.id = o.status and o.type = ot.id and o.region_id = r.id and o.status != 28 $addClause  and o.sl_id = sl.id";
//$sQuery = "
//	select SQL_CALC_FOUND_ROWS o.id , h.name as hub, o.order_no, o.order_date , ot.description as type ,o.customer_name as customer_name, s.title as status ,o.total_qty as qty, o.total_value as value, date(o.order_delivery_date) as order_delivery_date, r.title, sl.name as slocname
//	from orders o , hubs h, status s, order_types ot, regions r, stock_locations sl
//	$sWhere
//	$sOrder
//	$sLimit
//";


// print $sQuery;
//error_log("\nMSL query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

// print_r($objs);

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
            if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }else if ($aColumns[$i] == 'order_no') {                 
//                 $row[] = $obj->order_no;
                 $row[] = '<a target = "_blank" href="logistics/report/items/oid='.$obj->id.'">'.$obj->order_no.'</a>';
             }else if($aColumns[$i] == 'order_date'){

                 // $row[] = $obj->order_date;
                 $row[] = date('d-m-Y', strtotime( $obj->order_date ));
             }else if($aColumns[$i] == 'customer_name'){
                 $row[] = $obj->customer_name;
             }else if($aColumns[$i] == 'status'){
                $row[] = $obj->status;//." ".$obj->slocname;
             }else if($aColumns[$i] == 'qty'){
                $row[] = $obj->qty;
             }else if($aColumns[$i] == 'value'){
                 $row[] = $obj->value;
             }else if($aColumns[$i] == 'hub'){
                 $row[] = $obj->hub;
             }else if($aColumns[$i] == 'type'){
                 $row[] = $obj->type;
             }else if($aColumns[$i] == 'city'){
                 $row[] = $obj->title;
             }else if($aColumns[$i] == 'order_delivery_date'){
                 $row[] = ddmmyy($obj->order_delivery_date);
             }
             else if($aColumns[$i] == 'action'){
                 //$objserial= serialize($obj);
                 // $row[] = '<type="button" class="btn btn-primary" onclick="clicked(' . $db->safe($obj->order_no) . ')">Download QR Code</button>';
//                   $row[] = '<input type="checkbox" class="ordersel" value="'.$obj->id.'"/>';
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
