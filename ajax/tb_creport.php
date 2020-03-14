 <?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$aColumns = array('createtime','crcode','invoiceamt','cpamt','difference');
$sColumns = array('createtime','crcode');

/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
 
 if($currStore->usertype != UserType::RFC){   
    $crid = isset($_GET['crid']) ? $_GET['crid'] : false;
   }else{
    $crid =  $currStore->crid;
 }
//$crid = isset($_GET['crid']) ? $_GET['crid'] : false;
$daterange = isset($_GET['drange']) ? $_GET['drange'] : false;
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
$sOrder = " order by p.createtime desc ";
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
if(trim($daterange)!="" && trim($daterange)!="-1"){
	$daterange = str_replace("'"," ",$daterange);
	$dates = explode("-",$daterange);
	// print_r($dates);
	$startDate = $dates[0];
	$startDate = str_replace('/', '-', $startDate);	
	$endDate = $dates[1];
	$endDate = str_replace('/', '-', $endDate);
	$startDate = explode("-", $startDate);
	$nstartdate = trim($startDate[2])."-".trim($startDate[1])."-".trim($startDate[0]);
	$endDate = explode("-", $endDate);
	$nenddate = trim($endDate[2])."-".trim($endDate[1])."-".trim($endDate[0]);
	$startDate = date("Y-m-d 00:00:00", strtotime($nstartdate));
	$endDate = date("Y-m-d 23:59:59", strtotime($nenddate));
}

/*
 * SQL queries
 * Get data to display
 */
if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
//select c.createtime, r.dispname as crcode, sum(c.total_amount) as invtotamt , sum(p.amount) as colltotamt from it_cr270001 c, it_payments_diary p, it_rfc_master r c.id = p.invoice_id and c.crid = r.id and c.status = 1 and c.crid=$crid and c.createtime >='$startDate' and c.createtime <= '$endDate' group by c.createtime order by p.createtime desc;
$sGroup = " group by date(c.createtime) ";
$sWhere .= " c.id = p.invoice_id and c.crid = r.id and c.status = 1 and c.crid=$crid and c.createtime >='$startDate' and c.createtime <= '$endDate'";
$sQuery = " select c.createtime, r.dispname as crcode, sum(round(c.total_amount)) as invtotamt , sum(round(p.amount)) as colltotamt from it_cr270001 c, it_payments_diary p, it_rfc_master r
        $sWhere  
        $sGroup
	$sOrder
	$sLimit
";
//print_r($sQuery);
//error_log("\nMSL poquery: ".$sQuery."\n",3,"tmp.txt");
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
    $total = 0;
    $row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'createtime') {
                $Date = date("d-m-Y", strtotime($obj->createtime));
                $row[] = $Date;
            }elseif($aColumns[$i] == 'crcode'){
                $row[] = $obj->crcode;
            }elseif($aColumns[$i] == 'invoiceamt'){
                $invtotamt = round($obj->invtotamt);
                $row[]= $invtotamt;
                //$row[] = $obj->invtotamt;
            }elseif($aColumns[$i] == 'cpamt'){
                $colltotamt = round($obj-> colltotamt);
                $row[]=  $colltotamt;
            }elseif($aColumns[$i] == 'difference'){
                $invtotamt = round($obj->invtotamt);
                $colltotamt = round($obj-> colltotamt);
                $difference = round(  $invtotamt - $colltotamt);
                $row[]=  $difference ;
                 }else{
               $row[] = "-";
            }
	}
	$rows[] = $row;
	$iTotal++;
}

//mm
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
