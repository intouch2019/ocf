<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('category','product','desc1','desc2','Thickness','Price','batchcode','qty');
$sColumns = array('c.name','p.name','p.desc1','p.desc2','p.thickness','p.price','s.batchcode','s.qty');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dcid = isset($_GET['dcid']) ? $_GET['dcid'] : false;
$date = isset($_GET['date']) ? $_GET['date'] : false;

if($date){
    $today = date("d-m-Y");
    if($date == $today){
        $date = FALSE;
    }
}




//error_log("\nMSL stockquery: ".$dcid."\n",3,"tmp.txt");
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
if($date){
    $sOrder = "";
}else { 
    $sOrder = " order by s.createtime desc ";
}

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
// select l.name,b.bin,p.name,s.qty from it_stock_current s ,it_locations l ,it_bins b ,it_products p  where l.id= s.createdat_locationid and b.id =s.bin_id and p.id= s.product_id;
if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}

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
//$sWhere .= " p.id = s.prodid and s.batchcode=gl.batchcode and  p.ctg_id = c.id and  s.dcid = $dcid and g.id = gl.grnid and u.id = g.uom_id";
//$sQuery = "select SQL_CALC_FOUND_ROWS c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode, round(s.qty, 4) as qty, round((s.qty * u.multply/((gl.length/1000)*p.kg_per_pc)),2) as noofpcs,
//    round(gl.totalrate * u.multply ,2) as totalrate,round(gl.rate*u.multply,2) as rate , round(round(s.qty,4) * round(gl.totalrate * u.multply ,2),2) as value ,s.createtime from it_products p,it_stockcurr s,it_categories c,it_grnitems gl  , it_grn g, it_uom u 
//        $sWhere 
//	$sOrder
//	$sLimit
//";
$addQry = "";
$addWhere = "";
$addSelColumn = "";
if($date){
    $addQry .= ",it_closing_stock";
    $closeDate = date('Y-m-d', strtotime($date));
    $addWhere .= " and s.stock_date = '$closeDate'";
    $addSelColumn .= ",s.stock_date";
    
}else {
    $addQry .= ",it_stockcurr";   
}


$sWhere .= " p.id = s.prodid and  p.ctg_id = c.id and  s.dcid = $dcid $addWhere";
$sQuery = "select SQL_CALC_FOUND_ROWS c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.price,p.stdlength,s.batchcode, s.qty as qty $addSelColumn
    from it_products p $addQry s,it_categories c
        $sWhere 
	$sOrder
	$sLimit
";
//check it_grn join if not required remove
// echo $sQuery;
//error_log("\nMSL stockquery: ".$sQuery."\n",3,"tmp.txt");
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
            if ($aColumns[$i] == 'category') {
                //show in ddmmyy
                $row[] = $obj->ctg;
            }else if($aColumns[$i] == 'product'){
                //show in ddmmyy
                $row[] = $obj->name;
            }else if($aColumns[$i] == 'desc1'){
                 $row[] = $obj->desc1;
//                $row[] = $obj->pono; 
            }else if($aColumns[$i] == 'desc2'){
               $row[] = $obj->desc2;
            }else if($aColumns[$i] == 'Thickness'){
               $row[] = $obj->thickness;
            }else if($aColumns[$i] == 'Price'){
               $row[] = $obj->price;
            }else if($aColumns[$i] == 'batchcode'){
               $row[] = $obj->batchcode;
            }else if($aColumns[$i] == 'qty'){
               $row[] = $obj->qty;
            }  
//            else if($aColumns[$i] == 'createtime'){
//               $row[] = $obj->transferdate;
//            }   
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
