<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";
require_once "lib/db/DBLogic.php";

$currStore = getCurrStore();
$dbl = new DBLogic();

$aColumns = array('id','prod_desc','desc1','desc2','thickness','currentprice','lastprice','diff','dccost','dccostdiff','crcost','crcostdiff','date','approved');
$sColumns = array('p.id','p.shortname','p.desc1','p.desc2','p.thickness','pr.price', 'pr.applicable_date');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$crid = $_GET['crid'];
$status = $_GET['status'];
$uploaddate = $_GET['uploaddate'];
$sdate=$uploaddate." 00:00:00";
$edate=$uploaddate." 23:59:59";

/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by p.id ";
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

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
//$aColumns = array('id','prod_desc','desc1','desc2','thickness','currentprice','lastprice','diff','date','approved');
//$sWhere .= " pr.product_id = p.id and pr.applicable_date = date(now()) and p.active = 1 and pr.crid = $crid and pr.status = $status $addClause ";
//$sWhere .= " pr.product_id = p.id and pr.applicable_date = date('$uploaddate') and p.active = 1 and pr.crid = $crid and pr.status = $status $addClause ";
$sWhere .= " pr.product_id = p.id and pr.applicable_date >= '".$sdate."' and  pr.applicable_date <='".$edate."' and p.active = 1 and pr.crid = $crid and pr.status = $status $addClause ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS p.id , p.shortname as itemname, p.desc1, p.desc2, p.thickness, pr.price, pr.lastprice, round(pr.price - pr.lastprice,2) as diff, pr.applicable_date, p.active, pr.is_approved, pr.status    
	from it_products p , it_product_price pr  
	$sWhere 
	$sOrder
	$sLimit
";
//error_log("\nMSL query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);
//echo $sQuery;
/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS        
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
//$aColumns = array('id','prod_desc','desc1','desc2','thickness','currentprice','lastprice','diff','date','approved');
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            $dcid = 1; // now we dont have dc and cr join so when new dc will created , we need change this code mayur(14/06/2019)
            $crCostObj = $dbl->getProdCostForCR($obj->id,$crid);
            $dcCostObj = $dbl->getProdCostForDC($obj->id,$dcid);
            $uploadedPrice = $obj->price;
            if($crCostObj->cost != null && $uploadedPrice > 0){
                $crCost = $crCostObj->cost;
                $crCostDiff = $uploadedPrice - $crCost;
            }else{
                $crCost = "-";
                $crCostDiff = "-";
            }
            if($dcCostObj->cost != null  && $uploadedPrice > 0){
                $dcCost = $dcCostObj->cost;
                $dcCostDiff = $uploadedPrice - $dcCost;
            }else{
                $dcCost = "-";
                $dcCostDiff = "-";
            }
            
            
            
            
            
            if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }else if($aColumns[$i] == 'prod_desc'){
                 $row[] = $obj->itemname;
             }else if($aColumns[$i] == 'desc1'){
                 $row[] = $obj->desc1;
             }else if($aColumns[$i] == 'desc2'){
                 $row[] = $obj->desc2;
             }else if($aColumns[$i] == 'thickness'){
                 $row[] = $obj->thickness;
             }else if($aColumns[$i] == 'currentprice'){
                 $row[] = $uploadedPrice;
             }else if($aColumns[$i] == 'lastprice'){
                $row[] = $obj->lastprice;                
             }else if($aColumns[$i] == 'diff'){
                $row[] = $obj->diff;                
             }else if($aColumns[$i] == 'dccost'){
                     $row[] = $dcCost;
             }else if($aColumns[$i] == 'dccostdiff'){
                     $row[] = $dcCostDiff;
             }else if($aColumns[$i] == 'crcost'){
                     $row[] = $crCost;
             }else if($aColumns[$i] == 'crcostdiff'){
                     $row[] = $crCostDiff;
             }else if($aColumns[$i] == 'date'){
                $row[] = ddmmyy($obj->applicable_date);  
             }else if($aColumns[$i] == 'approved'){
                 if($obj->status == ProductPriceStatus::Pending){
                    $approvestatus = ProductPriceStatus::Approved;
                    $disapprovestatus = ProductPriceStatus::Disapproved;
                    $row[] = '<input type="radio" class="btn btn-primary" name="rdbapprove" id="rdbapprove" value="approve" onclick="approve('.$obj->id.','.$approvestatus.')"/>Approve&nbsp;&nbsp;&nbsp;'
                            . '<input type="radio" class="btn btn-primary" name="rdbapprove" id="rdbapprove" value="disapprove" onclick="disapprove('.$obj->id.','.$disapprovestatus.')"/>DisApprove';
                 }else{
                     $status = ProductPriceStatus::getName($obj->status);
                     $row[] = $status;
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
