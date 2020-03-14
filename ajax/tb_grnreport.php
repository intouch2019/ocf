<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

$aColumns = array('dc','grnno','product','batchcode','qty','noodpcs','value','Createtime');
$sColumns = array('d.dc_name','g.grnno','p.name','gl.batchcode','gl.qty','gl.no_of_pieces','gl.totalvalue','g.grndate');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dcid = isset($_GET['dcid']) ? $_GET['dcid'] : false;
//error_log("\nMSL grnquery: ".$dcid."\n",3,"tmp.txt");
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
$sOrder = " order by gl.createtime desc ";
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
//if($sWhere==""){
//    $sWhere .= " where ";
//}else{
//    $sWhere .= " and ";
//}

//$dtClause= "";
//if(trim($purin_dt)!=""  && trim($purin_dt)!="select" && trim($purin_dt)!="Select Date" && trim($purin_dt) != null){
//    $dt = yymmdd($purin_dt);
//    $purin_dt_db = $db->safe($dt);
//    $dtClause .= " and  p.purin_dt = $purin_dt_db ";
//}else{
//    $srtdt= $db->safe(date("Y-m-01"));
//    $enddt= $db->safe(date("Y-m-d"));
//    $dtClause .= " and  p.purin_dt between $srtdt and $enddt";
//}

//////select p.pur_in_no,date(p.purin_dt),date(c.createtime),sum(ci.difference) from it_purchase_in p, it_conversions c , it_conversion_items ci where c.purchase_in_id = p.id and ci.conversion_id = c.id group by ci.conversion_id;
//$groupby = " group by ci.conversion_id";    
$sWhere .= " where g.dcid=d.id and g.id=gl.grnid and p.id=gl.product_id and g.dcid = $dcid";
$sQuery = "select SQL_CALC_FOUND_ROWS d.dc_name,g.grnno,p.name,gl.batchcode,p.desc1,p.desc2,p.thickness,p.hsncode,gl.qty,gl.no_of_pieces,gl.totalrate,gl.cgstval,gl.sgstval,gl.totalvalue,g.grndate,g.createtime from it_dc_master d,it_grn g,it_grnitems gl,it_products p
        $sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL grnquery: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;
//$aColumns = array('category','product','desc1','desc2','Thickness','HSN','batchcode','qty','createtime');
$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            /*'dc','grnno','product','batchcode','desc1','desc2','Thickness','HSN','qty','noodpcs','rate','cgst','sgst','value','Createtime'*/
            /*select d.dc_name,g.grnno,p.name,gl.batchcode,p.desc1,p.desc2,p.thickness,p.hsncode,gl.qty,gl.no_of_pieces,gl.totalrate,gl.cgstval,gl.sgstval,gl.totalvalue,gl.createtime*/
            if ($aColumns[$i] == 'dc') {
                //show in ddmmyy
                $row[] = $obj->dc_name;
            }else if($aColumns[$i] == 'grnno'){
                //show in ddmmyy
                $row[] = $obj->grnno;
            }else if($aColumns[$i] == 'product'){
                 $row[] = $obj->name;
//                $row[] = $obj->pono; 
            }else if($aColumns[$i] == 'batchcode'){
               $row[] = $obj->batchcode;
            }else if($aColumns[$i] == 'desc1'){
               $row[] = $obj->desc1;
            }else if($aColumns[$i] == 'desc2'){
               $row[] = $obj->desc2;
            }else if($aColumns[$i] == 'Thickness'){
               $row[] = $obj->thickness;
            }else if($aColumns[$i] == 'HSN'){
               $row[] = $obj->hsncode;
            }else if($aColumns[$i] == 'qty'){
               $row[] = $obj->qty;
            }else if($aColumns[$i] == 'noodpcs'){
               $row[] = $obj->no_of_pieces;
            }else if($aColumns[$i] == 'rate'){
               $row[] = $obj->totalrate;
            }else if($aColumns[$i] == 'cgst'){
               $row[] = $obj->cgstval;
            }else if($aColumns[$i] == 'sgst'){
               $row[] = $obj->sgstval;
            }else if($aColumns[$i] == 'value'){
               $row[] = $obj->totalvalue;
            }else if($aColumns[$i] == 'Createtime'){
               $row[] = $obj->grndate;
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
