<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('Name','createdtime','stockQty','desc1','desc2','thickness','stdlength','Add Quantity','Action');
$sColumns = array('Name','createdtime','stockQty','desc1','desc2','thickness','stdlength');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;

$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by a.createtime desc ";
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

$sWhere .= " where b.active=1  and a.prodid=b.id and a.crid= $crid";

$sQuery = "select SQL_CALC_FOUND_ROWS max(a.prodid) as prodId,SUM(round(a.qty,4)) as stockQty , max(a.createtime) as createdtime,max(b.name) as Name,max(b.desc1) as desc1,max(b.desc2) as desc2,max(b.thickness) as thickness,max(stdlength) as stdlength from it_stockcurr a,it_products b  
        $sWhere 
        group by a.prodid
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


$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      
    
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
           
            
            if ($aColumns[$i] == 'Name') {               
                $row[] = $obj->Name;
            }else if($aColumns[$i] == 'createdtime'){
                $row[] = $obj->createdtime;
            }else if($aColumns[$i] == 'stockQty'){
                 $row[] = $obj->stockQty;
            }else if($aColumns[$i] == 'desc1'){
               $row[] = $obj->desc1;
            }else if($aColumns[$i] == 'desc2'){
               $row[] = $obj->desc2;
            }else if($aColumns[$i] == 'thickness'){
               $row[] = $obj->thickness;
            } else if($aColumns[$i] == 'stdlength'){
               $row[] = $obj->stdlength;
            }
           else if($aColumns[$i] == 'Add Quantity'){
                $row[] = '<input  id="t_'.$obj->prodId.'" type="text" name="t_'. $obj->prodId .'" value="0" onkeyup=""/>'; 
           }
            else if($aColumns[$i] == 'Action'){
                
                $row[] = '<input type="button" class="btn btn-primary" name="Add" value="ADD" onclick="updateStock('.$obj->prodId .')"/>'; 
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
	
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
