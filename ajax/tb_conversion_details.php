<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('pono','prodname','purchase_qty','packsize','req_packets','act_packets','difference','reason');
$sColumns = array('p.name','pi.qty_in_kg','ps.pack_size');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$cid = isset($_GET['cid']) ? $_GET['cid'] : false;
//$purin_dt = isset($_GET['purdt']) ? $_GET['purdt'] : false;
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
$sOrder = " order by c.updatetime desc ";
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
//$lClause="";
//if(trim($pid)!="" && trim($pid)!="0" && $pid != null){
//    $lClause .= "  and pi.pur_in_id = $pid ";
//}
//$dtClause= "";

//select p.pur_in_no as pono, pr.name as prodname, ci.purchase_qty, ci.req_packets,ci.tot_packets, ci.difference, ci.reason from it_products pr, it_purchase_in p, it_conversions c, it_conversion_items ci where ci.conversion_id = c.id and c.purchase_in_id = p.id and ci.product_id = pr.id;

//if(trim($purin_dt)!=""  && trim($purin_dt)!="select" && trim($purin_dt)!="Select Date" && trim($purin_dt) != null){
//    $dt = yymmdd($purin_dt);
//    $purin_dt_db = $db->safe($dt);
//    $dtClause .= " and  pin.purin_dt = $purin_dt_db ";
//}
//    
$sWhere .= " ci.conversion_id = c.id and c.purchase_in_id = p.id and ci.product_id = pr.id and pr.pack_size_id = ps.id";
$sQuery = "select p.pur_in_no as pono, pr.name as prodname, ps.pack_size as packsize, ci.purchase_qty, ci.req_packets,ci.tot_packets, ci.difference, ci.reason
           from it_products pr, it_purchase_in p, it_conversions c, it_conversion_items ci , it_pack_size ps
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
{ 
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'pono') {
                $row[] = $obj->pono;
            }else if($aColumns[$i] == 'prodname'){
                $row[] = $obj->prodname;
            }else if($aColumns[$i] == 'purchase_qty'){
                $row[] = $obj->purchase_qty; 
            }else if($aColumns[$i] == 'packsize'){
                $row[] = $obj->packsize; 
            }else if($aColumns[$i] == 'req_packets'){
                $row[] = $obj->req_packets; 
            }else if($aColumns[$i] == 'act_packets'){
                $row[] = $obj->tot_packets; 
            }else if($aColumns[$i] == 'difference'){
               $row[] = $obj->difference;
            }else if($aColumns[$i] == 'reason'){
               $row[] = $obj->reason;
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
