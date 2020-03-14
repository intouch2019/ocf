<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

$aColumns = array('pono','product','desc1','desc2','thickness','hsncode','sku','qty','noofpcs','rate','lcrate','cgst','sgst','totalrate','totalvalue','createtime');
$sColumns = array('pr.pono','p.name','p.desc1','p.desc2','p.thickness','p.hsncode','pl.sku','pl.qty','pl.no_of_pieces','pl.rate','pl.lcrate','pl.cgstval','pl.sgstval','pl.totalrate','pl.totalvalue','pl.createtime');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dcid = isset($_GET['dcid']) ? $_GET['dcid'] : false;
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
$sOrder = " order by pr.createtime desc ";
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

$addClause = "";

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

    $addClause .= " and pr.createtime BETWEEN '$startDate' and '$endDate'";
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
$sWhere .= "p.id = pl.product_id and pr.id = pl.po_id and pr.delivery_id = $dcid";
$sQuery = "select SQL_CALC_FOUND_ROWS pr.pono,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,pl.sku,pl.qty,pl.no_of_pieces,pl.rate,pl.lcrate,
           pl.cgstval,pl.sgstval,pl.totalrate,pl.totalvalue,pl.createtime from it_products p,it_polines pl,it_purchaseorder pr
        $sWhere 
        $addClause    
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL poquery: ".$sQuery."\n",3,"tmp.txt");
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
            /*pr.pono,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,pl.sku,pl.qty,pl.no_of_pieces,pl.rate,pl.lcrate,
           pl.cgstval,pl.sgstval,pl.totalrate,pl.totalvalue,pl.createtime*/
            if ($aColumns[$i] == 'pono') {
                //show in ddmmyy
                $row[] = $obj->pono;
            }else if($aColumns[$i] == 'product'){
                //show in ddmmyy
                $row[] = $obj->name;
            }
//            else if($aColumns[$i] == 'desc1'){
//                 $row[] = $obj->desc1;
////                $row[] = $obj->pono; 
//            }else if($aColumns[$i] == 'desc2'){
//               $row[] = $obj->desc2;
//            }else if($aColumns[$i] == 'thickness'){
//               $row[] = $obj->thickness;
//            }else if($aColumns[$i] == 'hsncode'){
//               $row[] = $obj->hsncode;
//            }else if($aColumns[$i] == 'sku'){
//               $row[] = $obj->sku;
//            }
            else if($aColumns[$i] == 'qty'){
               $row[] = $obj->qty;
            }else if($aColumns[$i] == 'noofpcs'){
               $row[] = $obj->no_of_pieces;
            }
//            else if($aColumns[$i] == 'rate'){
//               $row[] = $obj->rate;
//            }else if($aColumns[$i] == 'lcrate'){
//               $row[] = $obj->lcrate;
//            }else if($aColumns[$i] == 'cgst'){
//               $row[] = $obj->cgstval;
//            }else if($aColumns[$i] == 'sgst'){
//               $row[] = $obj->sgstval;
//            }else if($aColumns[$i] == 'totalrate'){
//               $row[] = $obj->totalrate;
//            }
            else if($aColumns[$i] == 'totalvalue'){
               $row[] = $obj->totalvalue;
            }else if($aColumns[$i] == 'createtime'){
               $row[] = $obj->createtime;
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
