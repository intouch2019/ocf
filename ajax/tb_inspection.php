<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

$aColumns = array('srno','product','billno','pono','qty','rate','value','receiveddate','action');
$sColumns = array('sbi.srno','p.name','sb.billno','pr.pono','sbi.qty','sbi.rate','sbi.value','sbi.receiveddate');

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
$sOrder = " order by sbi.receiveddate asc ";
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

$status = SupplierBillStatus::Submit;
$sWhere .= " sb.id = sbi.bill_id and p.id = sbi.product_id and sb.status = $status and pr.id = sbi.poid and sbi.inspected = 0 $addClause ";
   
$sQuery = "
	select SQL_CALC_FOUND_ROWS @a:=@a+1 srno,sbi.id,p.name as product,sb.billno,pr.pono,sbi.qty,sbi.rate,sbi.rate*sbi.qty as value,
        sbi.receiveddate from it_products p, it_purchaseorder pr, it_supplier_bill sb, it_supplier_bill_items sbi,(select @a:=0) as a 
	$sWhere 
	$sOrder
	$sLimit
";

//error_log("\nMSL query: ".$sQuery."\n",3,"tmp1.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS        
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;
$rows = array(); $iTotal=0;
//$aColumns = array('product','billno','pono','qty','rate','value','receiveddate','action');
$srno = 1;
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
             if ($aColumns[$i] == 'srno') {
                 $row[] = $obj->srno;
             }else if ($aColumns[$i] == 'product') {
                 $row[] = $obj->product;
             }else if ($aColumns[$i] == 'billno') {
                 $row[] = $obj->billno;
             }else if($aColumns[$i] == 'pono'){
                 $row[] = $obj->pono;
             }else if($aColumns[$i] == 'qty'){
                 $row[] = $obj->qty;
             }else if($aColumns[$i] == 'rate'){
                 $row[] = $obj->rate;
             }else if($aColumns[$i] == 'value'){
                 $row[] = $obj->value;
             }else if($aColumns[$i] == 'receiveddate'){
                 $row[] = ddmmyy($obj->receiveddate);
             }else if($aColumns[$i] == 'action'){
                    $row[] = '<button class="btn btn-primary" type="button" onclick="accept(' . $obj->id . ')">Accept</button>&nbsp;'
                            . '<button class="btn btn-primary" type="button" onclick="reject(' . $obj->id . ')">Reject</button>';
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
