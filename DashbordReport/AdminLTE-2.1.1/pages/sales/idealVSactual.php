<?php
//include '/var/www/weikfield_kiosks/it_config.php';
include '/home/weikfield/public_html/kiosks/it_config.php';
require_once "lib/core/Constants.php";
require_once "lib/db/DBConn.php";
require_once "session_check.php";
?>


<html>
    <head>
        <meta charset="UTF-8">
        <title>Weikfield Kiosk</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- Bootstrap 3.3.4 -->
        <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Font Awesome Icons -->
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="../../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <!-- AdminLTE Skins. Choose a skin from the css/skins 
             folder instead of downloading all of them to reduce the load. -->
        <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- daterange picker -->
        <link href="../../plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap time Picker -->
        <link href="../../plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet"/>
        <!-- bootstrap datepicker -->
        <link href="../../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />

    </head>
    <body class="skin-green sidebar-mini">
        <div class="wrapper">

            <header class="main-header">
                <!-- Logo -->
                <a href="../../index2.html" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>WF</b></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>WeikField</b></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <!--                    <div class="navbar-custom-menu">
                                            <ul class="nav navbar-nav">
                                                <li class="dropdown user user-menu">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                        <span style = "font-size: 25px"><Strong>Chirag</strong></span>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                    </ul>
                                                </li>
                    
                                            </ul>
                                        </div>-->
                </nav>
            </header>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->

                <div class="box box-success">
                    <div class="box-header">
                        <section class="content-header">
                            <h2>
                                Ideal VS Actual Consumption
                            </h2>
                        </section>  
                        <hr style="display: block;margin-top: 0.5em;margin-bottom: 0.5em;margin-left: auto;margin-right: auto;border-style: inset;border-width: 1px;">
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-4" style="margine-left:5em">
                                <label>Select Store:</label>
                                <select id="store" name="store" class="form-control" onchange="loadStoreWise(this.value);" >
                                    <option value = "-1" selected="true">Select Store</option>
                                    <?php
                                    $db = new DBConn();
                                    $store_id = "";
                                    $store_id = $_GET["sid"];
                                    $date = "";
                                    $date = $_GET["date"];
                                    $query = "select id,name from it_stores";
                                    $obj = $db->fetchObjectArray($query);
                                    foreach ($obj as $store) {
                                        $selected = "";
                                        if (isset($store_id) && $store_id == $store->id && trim($store_id) != "") {
                                            $selected = "selected";
                                        }
                                        ?>
                                        <option value = "<?php echo $store->id; ?>" <?php echo $selected; ?>><?php echo $store->name; ?></option>
                                        <?php
                                    }
                                    ?>   
                                </select>
                            </div>
                            <div class="col-xs-5" style="margin-left: 2em">
                                <div class="form-group">
                                    <label>Select Date range:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <?php
                                        if (isset($date) && $date != "") {
                                            ?>

                                            <input type="text" class="form-control pull-right" id="reservation" onchange="loadDateWise(this.value);" value="<?php echo $date; ?>"/>
                                            <?php
                                        } else {
                                            ?>
                                            <input type="text" class="form-control pull-right" id="reservation" onchange="loadDateWise(this.value);"/>
                                            <?php
                                        }
                                        ?>
                                    </div> <!--/.input group -->
                                </div> <!--/.form group   --> 
                            </div>
                            <?php if (isset($store_id) && $store_id != "-1") {
                                ?>

                                <div class="col-xs-2" style="margin-left: 2em">
                                    <div class="form-group">
                                        <label>Excel Export:- </label>
                                        <div class="input-group">
                                            <button onclick="downloadStockandConsumptionData();">Export</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div> <!--/.box-body -->
                </div> <!--/.box -->
                <!--  Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped" style ="font-size: 12px">
                                        <thead>
                                            <tr>
                                                <th>Ingredients</th>
                                                <th>Measure</th>
                                                <th>Price</th>
                                                <th>Op Stk</th>
                                                <th>GRN</th>
                                                <th>Tr In</th>
                                                <th>Tr Out</th>
                                                <th>Waste</th>
                                                <th>I C</th>
                                                <th>A C</th>
                                                <th>Var</th>
                                                <th>I S</th>
                                                <th>A S</th>
                                                <th>Var</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($store_id) && trim($store_id) != "" && trim($store_id) != "-1" && isset($date) && $date != "") {
                                                if (isset($date) && $date != "") {
                                                    list($date_1, $date_2) = preg_split("/-/", $date);
                                                    list($mm, $dd, $yy) = preg_split("/\//", $date_1);
                                                    // $date_1 = $db->safe($yy . "-" . $mm . "-" . $dd);
                                                    $date_1 = $yy . "-" . $mm . "-" . $dd;
                                                    $date_1 = str_replace(' ', '', $date_1);
                                                    list($mm, $dd, $yy) = preg_split("/\//", $date_2);
                                                    $date_2 = $yy . "-" . $mm . "-" . $dd;
                                                    $date_2 = str_replace(' ', '', $date_2);
                                                    //get yesterday's date
                                                    $day_before = date('Y-m-d', strtotime($date_1 . ' -1 day'));
                                                    $ideal_stock = 0;
                                                    $actual_stock = 0;
                                                    $array1=array();
                                             
                                                    $query = "select max(date(i.indent_dttm)) as latest_indent from it_indents i where i.indent_dttm < '$date_1' and i.store_id=$store_id";
                                                    $obj = $db->fetchObject($query);
                                                    if (isset($obj)) {
                                                        $latest_indent = $obj->latest_indent;
                                                    }

                                                    if (isset($latest_indent) && trim($latest_indent) != "") {
                                                        //If latest stock upload date and selected daterange starting date's yesterday date is same 
                                                        if ($latest_indent == $day_before) {
                                                            $day_before = $latest_indent;
                                                        }

                                                        //get purchase (These are the actual items to display)
                                                        $query = "select pr.id,pr.name,pr.uom,sp.price from it_store_products sp,it_purchases p,it_products pr,it_purchase_items pi where pi.product_id=sp.id and pr.id=sp.product_id and sp.store_id=p.store_id and sp.store_id=$store_id and date(p.order_date) between '$date_1' and '$date_2' group by pr.id order by pr.name asc ";
//                                                        print $query;
                                                        $result = $db->getConnection()->query($query);
                                                        
                                                        while($obj = $result->fetch_object()){
                                                            
                                                            if(in_array($obj->id, $array1)){
                                                                continue;
                                                            }else{
                                                                $array1[] = $obj;
                                                            }
                                                            }
                                                        
                                                        //get sells
                                                        $query1 = "select p.id,p.name,p.uom,sp.price from it_store_products sp,it_orders o,it_products p,it_order_items oi where oi.product_id=sp.id and p.id=sp.product_id and sp.store_id=o.store_id and sp.store_id=$store_id and date(o.order_date) between '$date_1' and '$date_2' group by p.id order by p.name asc ";
                                                        // print $query1;
                                                        $result1 = $db->getConnection()->query($query1);
                                                        
                                                         while($obj = $result1->fetch_object()){
                                                            
                                                            if(in_array($obj->id, $array1)){
                                                                continue;
                                                            }else{
                                                                $array1[] = $obj;
                                                            }
                                                            }
                                                        
                                                            foreach($array1 as $obj1){
                                                                $product_id = $obj1->id;
                                                                
                                                                  $qry1 = "select rb.* from it_products p,it_store_products sp,it_recipe_breakdown rb where rb.recipe_product_id=p.id and p.id=sp.product_id and rb.product_id=$product_id and sp.store_id=$store_id";
                                                                 $objs5 = $db->getConnection()->query($qry1);
                                                                
                                                                if(isset($objs5)){
                                                                    $qry1 = "select p.*,sp.price from it_products p,it_store_products sp,it_recipe_breakdown rb where rb.recipe_product_id=p.id and p.id=sp.product_id and rb.product_id=$product_id and sp.store_id=$store_id";
                                                                }else{
                                                                    $qry1 = "select p.*,sp.price from it_products p,it_store_products sp where p.id=sp.product_id and sp.product_id=$product_id  and sp.store_id=$store_id";
                                                                }                                                                  
                                                              
                                                                $objs5 = $db->getConnection()->query($qry1);
                                                                
                                                                while ($obj1 = $objs5->fetch_object()) {
                                                                    $product_id = $obj1->id;
                                                                   
                                                                    if (!isset($obj1->uom)) {
                                                                        $uom = "-";
                                                                    } else {
                                                                        $uom = $obj1->uom;
                                                                    }
                                                                 
                                                                    if(in_array($obj1->id,$array1)){
                                                                        continue;
                                                                    }else{
                                                                        $array1[] = $obj1->id;
                                                                    }

                                                                    //Opening stock     
                                                                    $qry2 = "select ii.curr_stock,sum(sd.quantity) as stock from it_indents i,it_indent_items ii,it_stock_diary sd where i.id=ii.indent_id and sd.product_id=$product_id and ii.product_id=$product_id and i.indent_dttm between '$latest_indent 00:00:00' and  '$day_before 23:59:59' and i.store_id=$store_id and sd.createtime between '$latest_indent 00:00:00' and  '$day_before 23:59:59'";
                                                                    //  print $qry2;  
                                                                    $query4 = $db->fetchObject($qry2);

                                                                    $opening_stock = $query4->curr_stock + $query4->stock;
                                                                    $opening_stock = round($opening_stock, 2);

                                                                    //GRN
                                                                
                                                                    $qry3 = "select sum(pi.quantity) as grn from it_purchases pu,it_purchase_items pi,it_store_products sp  where sp.store_id=pu.store_id and pi.product_id=sp.id and  pu.id=pi.purchase_id and sp.product_id=$product_id and sp.store_id=$store_id and date(pu.order_date) between '$date_1' and '$date_2'";
                                                                    $qry = $db->fetchObject($qry3);
                                                                    if (isset($qry) && $qry->grn != null) {
                                                                        $grn_number = $qry->grn;
                                                                        $grn_number = round($grn_number, 2);
                                                                    } else {
                                                                        $grn_number = 0;
                                                                    }
                                                                    //Transfer in and transfer out
                                                                    $transfer_in = 0;
                                                                    $transfer_out = 0;

                                                                    //damage
                                                                  
                                                                  // $query3 = $db->fetchObject("select sum(sd.quantity) as damage_qty from it_products p,it_stock_diary sd,it_transactions t,it_transaction_items ti,it_store_products sp  where sp.product_id=p.id and sp.store_id=t.store_id and ti.product_id=sp.id and  t.id=ti.transaction_id and  sd.product_id=p.id and p.id=$product_id and sd.store_id=$store_id and t.tran_date between '$date_1 00:00:00' and '$date_2 23:59:59' and t.transaction_number=3 ;");
                                                                     $query3 = $db->fetchObject("select sum(ti.quantity) as damage_qty from it_transactions t,it_transaction_items ti,it_store_products sp  where  sp.store_id=t.store_id and ti.product_id=sp.id and  t.id=ti.transaction_id and sp.product_id=$product_id and sp.store_id=$store_id and t.tran_date between '$date_1 00:00:00' and '$date_2 23:59:59' and t.transaction_number=3 ;");
                                                                    if (isset($query3) && trim($query3->damage_qty) != null) {
                                                                        $damage_quantity = $query3->damage_qty;
                                                                        $damage_quantity = round($damage_quantity, 2);
                                                                    } else {
                                                                        $damage_quantity = 0;
                                                                    }
                                                                    //ideal consumption
                                                                    $query4 = $db->fetchObject("select sum(oi.item_qty) as ideal_consumption from it_orders o,it_order_items oi,it_store_products sp where sp.id=oi.product_id and o.id=oi.order_id and o.createtime between '$date_1 00:00:00' and '$date_2 23:59:59' and sp.product_id=$product_id and o.store_id=$store_id");
                                                                    if (isset($query4) && trim($query4->ideal_consumption) != null) {
                                                                        $ideal_consumption = $query4->ideal_consumption;
                                                                        $ideal_consumption = round($ideal_consumption, 2);
                                                                    } else {
                                                                        $ideal_consumption = 0;
                                                                    }

                                                                    //Ideal stock

                                                                    $ideal_stock = $opening_stock + $grn_number + $transfer_in - $transfer_out - $damage_quantity - $ideal_consumption;
                                                                    $ideal_stock = round($ideal_stock, 2);

                                                                    //Actual consumption
                                                                    $actual_consumption = $opening_stock + $grn_number + $transfer_in - $transfer_out - $damage_quantity - $actual_stock;
                                                                    $actual_consumption = round($actual_consumption, 2);
                                                                    //actual-ideal consumption
                                                                    $var_con = $actual_consumption - $ideal_consumption;



                                                                    //actual stock
                                                                    $query4 = "select indent_dttm from it_indents where indent_dttm like '%$date_2%' order by id desc limit 1";
                                                                    //print($query4);
                                                                    $qry = $db->fetchObject($query4);
                                                                    if (isset($qry)) {
                                                                        $latest_indent1 = $qry->indent_dttm;
                                                                        // $query1 = "select sum(ii.curr_stock) as stock from it_indents i,it_indent_items ii,it_store_products sp,it_products p  where p.id=sp.product_id and  sp.id=ii.product_id and i.id=ii.indent_id and i.indent_dttm between '$date_1 23:59:59' and '$date_2 23:59:59' and i.store_id=$store_id and sp.product_id=$product_id";
                                                                        // print($query1);
                                                                        $query1 = "select ii.curr_stock,sum(sd.quantity) as actual_stock from it_indents i,it_indent_items ii,it_stock_diary sd where i.id=ii.indent_id and sd.product_id=$product_id and i.store_id=$store_id and i.indent_dttm like '%$latest_indent1%'";
                                                                        //print($query1);  
                                                                        $result = $db->fetchObject($query1);
                                                                        if (isset($result)) {
                                                                            $actual_stock = $result->actual_stock;
                                                                            $actual_stock = round($actual_stock, 2);
                                                                        }
                                                                    } else {
                                                                        $actual_stock = 0;
                                                                    }
                                                                    //actual-ideal stock
                                                                    $var_stock = $actual_stock - $ideal_stock;
                                                                    $var_stock = round($var_stock, 2);
                                                                    
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $obj1->name; ?></td>
                                                                        <td><?php echo $uom ?></td>
                                                                        <td><?php echo $obj1->price ?></td>
                                                                        <td><?php echo $opening_stock ?></td>
                                                                        <td><?php echo $grn_number ?></td>
                                                                        <td><?php echo $transfer_in ?></td>
                                                                        <td><?php echo $transfer_out ?></td>
                                                                        <td><?php echo $damage_quantity ?></td>
                                                                        <td><?php echo $ideal_consumption ?></td>
                                                                        <td><?php echo $actual_consumption ?></td>
                                                                        <td><?php echo $var_con ?></td>
                                                                        <td><?php echo $ideal_stock ?></td>
                                                                        <td><?php echo $actual_stock ?></td>
                                                                        <td><?php echo $var_stock ?></td>
                                                                    </tr> 

                        <?php
                    }
                //}
            }
        } else {
            //echo "Stock is not uploaded";
        }
    }
}
?>  

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Ingredients</th>
                                                <th>Measure</th>
                                                <th>Price</th>
                                                <th>Op Stk</th>
                                                <th>GRN</th>
                                                <th>Tr In</th>
                                                <th>Tr Out</th>
                                                <th>Waste</th>
                                                <th>I C</th>
                                                <th>A C</th>
                                                <th>Var</th>
                                                <th>I S</th>
                                                <th>A S</th>
                                                <th>Var</th>
                                            </tr>

                                        </tfoot>
                                    </table>
                                </div> <!--/.box-body -->
                            </div> <!--/.box -->
                        </div> <!--/.col -->
                    </div> <!--/.row -->
                </section> <!--/.content -->
            </div><!-- /.content-wrapper -->


            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <!-- search form -->
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="treeview">
                            <a href="../sales/sales.php">
                                <i class="fa fa-file-text-o"></i> <span>Sales</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>    
                        <li class="treeview">

                            <a href="../sales/sale_item.php">
                                <i class="fa fa-file-text-o"></i> <span>Item wise Sales</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">

                            <a href="../sales/expenses.php">
                                <i class="fa fa-file-text-o"></i> <span>Expense</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">

                            <a href="../sales/grn.php">
                                <i class="fa fa-file-text-o"></i> <span>GRN</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">

                            <a href="../sales/wastage.php">
                                <i class="fa fa-file-text-o"></i> <span>Wastage</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  
                        <li class="treeview">
                            <a href="../sales/idealVSactual.php">
                                <i class="fa fa-file-text-o"></i> <span>ConSumption Report</span> <i class="fa fa-angle-right pull-right"></i>
                            </a>
                        </li>  

                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>



            <footer class="main-footer">
                <strong>Copyright &copy; 2014-2015 <a href="http://onintouch.com">  OnIntouch</a>.</strong> All rights reserved.
            </footer>

            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class='control-sidebar-bg'></div>
        </div><!-- ./wrapper -->

        <!-- jQuery 2.1.4 -->
        <script src="../../plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <!-- Bootstrap 3.3.2 JS -->
        <script src="../../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="../../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="../../plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <!-- SlimScroll -->
        <script src="../../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <!-- FastClick -->
        <script src='../../plugins/fastclick/fastclick.min.js'></script>
        <!-- AdminLTE App -->
        <script src="../../dist/js/app.min.js" type="text/javascript"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../dist/js/demo.js" type="text/javascript"></script>
        <!-- date-range-picker -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
        <script src="../../plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- bootstrap color picker -->
        <script src="../../plugins/colorpicker/bootstrap-colorpicker.min.js" type="text/javascript"></script>
        <!-- bootstrap time picker -->
        <script src="../../plugins/timepicker/bootstrap-timepicker.min.js" type="text/javascript"></script>
        <!-- bootstrap datepicker -->
        <script src="../../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
        <!-- page script -->

        <script type="text/javascript">
                                                $(function() {
                                                    $("#example1").dataTable();                 //Date range picker
                                                    $('#reservation').daterangepicker();
                                                    //Date range as a button
                                                    $('#daterange-btn').daterangepicker(
                                                            {
                                                                ranges: {
                                                                    'Today': [moment(), moment()],
                                                                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                                                                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                                                                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                                                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                                                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                                                                },
                                                                startDate: moment().subtract('days', 29),
                                                                endDate: moment()
                                                            },
                                                    function(start, end) {
                                                        $('#reportrange-span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                                                    }

                                                    );
                                                });


                                                function loadStoreWise(store_id) {
                                                    var date = document.getElementById('reservation').value;

                                                    if (date != "") {
                                                        window.location.href = "?sid=" + store_id + "&date=" + date;
                                                    } else {
                                                        alert("Please select date range");
                                                    }


                                                }
                                                function loadDateWise(date) {
                                                    var store = document.getElementById('store').value;
                                                    if (store != "" && store != -1) {
                                                        window.location.href = "?date=" + date + "&sid=" + store;
                                                    } else {
                                                        alert("Please select store");
                                                    }

                                                }
                                                function downloadStockandConsumptionData() {

                                                    var store = document.getElementById('store').value;
                                                    var date = document.getElementById('reservation').value;
//                                                alert(store+":"+date);
                                                    if (store !== -1 && date === "") {
                                                        window.location.href = "../../../../formpost/generateStockandConsumption.php?sid=" + store;
                                                    } else if (store !== -1 && date !== "") {
                                                        window.location.href = "../../../../formpost/generateStockandConsumption.php?sid=" + store + "&date=" + date;
                                                    }
                                                }
        </script>

    </body>
</html>
