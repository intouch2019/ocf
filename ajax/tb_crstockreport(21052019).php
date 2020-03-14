<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('category','product','desc1','desc2','Thickness','HSN','batchcode','qty','price','value');
$sColumns = array('c.name','p.name','p.desc1','p.desc2','p.thickness','p.hsncode','s.batchcode','s.qty','price','value');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;
$uploaddate = isset($_GET['uploaddate']) ? $_GET['uploaddate'] : false;

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

$sWhere .= " where p.id = s.prodid and s.prodid = pr.product_id and  p.ctg_id = c.id  and pr.applicable_date ='".$uploaddate."'  and s.crid = $crid and pr.crid = $crid";
$sQuery = "select SQL_CALC_FOUND_ROWS c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,4) as qty,pr.price as price,round(round(s.qty,4) * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr
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
$iFilteredTotal = $obj->TOTAL_ROWS;

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
            }else if($aColumns[$i] == 'HSN'){
               $row[] = $obj->hsncode;
            }else if($aColumns[$i] == 'batchcode'){
               $row[] = $obj->batchcode;
            }else if($aColumns[$i] == 'qty'){
                $roundQty = round($obj->qty,4,PHP_ROUND_HALF_UP);
                $roundQty = sprintf("%.4f",$roundQty);
                $row[] = $roundQty;
            }else if($aColumns[$i] == 'price'){
                $roundPrice = round($obj->price,2);
                $roundPrice = sprintf("%.2f",$roundPrice);
                $row[] = $roundPrice;
            }else if($aColumns[$i] == 'value'){
               $roundValue = round( $obj->value,2);
               $roundValue = sprintf("%.2f",$roundValue);
               $row[] = $roundValue;
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
