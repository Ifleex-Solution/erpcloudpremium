<?php
if (isset($_POST['btnSearch'])) {
    $postdate = $_POST['alldata'];
}
$searchdate = (!empty($postdate) ? $postdate : date('F Y'));

?>


<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-green whitecolor">
            <div class="inner">
                <h4><span class="count-number">
                        <?php echo html_escape($total_customer) ?>
                    </span></h4>
                <p>
                    <?php echo display('total_customer') ?>
                </p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <?php if ($this->permission1->method('manage_customer', 'read')->access()) { ?>
                <a href="<?php echo base_url('customer_list') ?>" class="small-box-footer">
                    <?php echo display('total_customer') ?>
                </a>
            <?php } else { ?>
                <a href="javascript:void(0)" class="small-box-footer">
                    <?php echo display('total_customer') ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-pase whitecolor">
            <div class="inner">
                <h4><span class="count-number">
                        <?php echo html_escape($total_product) ?>
                    </span></h4>

                <p>
                    <?php echo display('total_product') ?>
                </p>
            </div>
            <div class="icon">
                <i class="fa fa-shopping-bag"></i>
            </div>
            <?php if ($this->permission1->method('manage_product', 'read')->access()) { ?>
                <a href="<?php echo base_url('product_list') ?>" class="small-box-footer">
                    <?php echo display('total_product') ?>
                </a>
            <?php } else { ?>
                <a href="javascript:void(0)" class="small-box-footer">
                    <?php echo display('total_product') ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-bringal whitecolor">
            <div class="inner">
                <h4><span class="count-number">
                        <?php echo html_escape($total_suppliers) ?>
                    </span></h4>

                <p>
                    <?php echo display('total_supplier') ?>
                </p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
            <?php if ($this->permission1->method('manage_supplier', 'read')->access()) { ?>
                <a href="<?php echo base_url('supplier_list') ?>" class="small-box-footer">
                    <?php echo display('total_supplier') ?>
                </a>
            <?php } else { ?>
                <a href="javascript:void(0)" class="small-box-footer">
                    <?php echo display('total_supplier') ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-darkgreen whitecolor">
            <div class="inner">
                <h4><span class="count-number">
                        <?php echo html_escape($total_sales) ?>
                    </span> </h4>

                <p>
                    <?php echo display('total_invoice') ?>
                </p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <?php if ($this->permission1->method('manage_invoice', 'read')->access()) { ?>
                <a href="<?php echo base_url('invoice_list') ?>" class="small-box-footer">
                    <?php echo display('total_invoice') ?>
                </a>
            <?php } else { ?>
                <a href="javascript:void(0)" class="small-box-footer">
                    <?php echo display('total_invoice') ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 d-flex"  style="height: 450px;">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="best-sale-title">
                        Trend Analysis (Bar Chart)
                    </h4>
                    <a href="<?php echo base_url(); ?>dashboard/home/see_all_best_sales"
                        class="btn btn-success text-white best-sale-seeall">See All</a>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="lineChart" height="160"></canvas>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 d-flex" style="height: 450px;">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="best-sale-title">
                        Trend Analysis (Pie Chart)
                    </h4>
                </div>
                <div class="row">

                    <div class="col-sm-8 marginpadding-right0">
                        <input type="text" class="form-control " value="<?php echo $searchdate; ?>" name="alldata"
                            id="alldata">
                    </div>
                    <div class="col-sm-2 marginpaddingleft0">
                        <button name="btnSearch" class="btn btn-success" onclick="searchTrendPie()"><i class="fa fa-search"></i>
                            <?php echo display('filter') ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div id="chartContainer" class="piechartcontainer"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="charttitle">Total Sale/Purchase Analysis</h4>
                </div>
                <div class="panel-body">
                    <canvas id="yearlyreport" width="600" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="charttitle">
                        <?php echo display('todays_sales_report') ?>
                    </h4>
                    <a href="<?php echo base_url(); ?>sales_report"
                        class="btn btn-success text-white best-sale-seeall">See All</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive todayssaletitle">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo display('sl') ?>
                                </th>
                                <th>
                                    <?php echo display('invoice_no') ?>
                                </th>
                                <th>
                                    <?php echo display('customer_name') ?>
                                </th>
                                <th>
                                    Payment Type
                                </th>

                                <th>
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            date_default_timezone_set('Asia/Colombo');

                            $ttl_amount = $ttl_paid = $ttl_due = $ttl_discout = $ttl_receipt = 0;
                            $todays = date('Y-m-d');
                            if ($todays_sales_report) {
                                $sl = 0;
                                foreach ($todays_sales_report as $single) {


                                    $sl++;
                            ?>
                                    <tr>
                                        <td>
                                            <?php echo $sl; ?>
                                        </td>
                                        <td>
                                            <a
                                                href="<?php echo base_url() . 'invoice_details/'.$single->invoice_id1; ?><?php echo html_escape($single->invoice_id); ?>">
                                                <?php echo html_escape($single->sale_id); ?>
                                            </a>

                                            <!-- $details = '  <a href="' . $base_url . 'invoice_details/' . $record->invoice_id . '" class="" >' . $record->invoice . '</a>'; -->

                                        </td>
                                        <td>

                                            <?php echo html_escape($single->customer_name); ?>

                                        </td>
                                        <td>

                                            <?php echo html_escape($single->pay); ?>

                                        </td>


                                        <td class="text-right">
                                            <?php
                                            $ttl_paid += $single->grandTotal;
                                            echo html_escape(number_format($single->grandTotal, '2', '.', ',')); ?>
                                        </td>





                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <th class="text-center" colspan="4">
                                        <?php echo display('not_found'); ?>
                                    </th>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>

                            <tr>
                                <td colspan="4" align="right">&nbsp;<b>
                                        <?php echo display('total') ?>:
                                    </b></td>
                                <td class="text-right">
                                    <?php
                                    $ttl_paid_float = html_escape(number_format($ttl_paid, '2', '.', ','));
                                    echo (($position == 0) ? "$currency $ttl_paid_float" : "$ttl_paid_float $currency"); ?>
                                </td>



                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>


        </div>

    </div>


</div>
<input type="hidden" id="bestsalelabel" value='<?php echo html_escape($chart_label); ?>' name="">
<input type="hidden" id="bestsaledata" value='<?php echo html_escape($chart_data); ?>' name="">
<input type="hidden"
    value='<?php
            // $seperatedData = explode(',', $chart_data); echo html_escape(($seperatedData[0] + 10));
            ?>' name=""
    id="bestsalemax">

<input type="hidden" id="month" value="<?php echo html_escape($month); ?>" name="">

<input type="hidden" id="tlvmonthsale" value="<?php echo html_escape($tlvmonthsale); ?>" name="">
<input type="hidden" id="tlvmonthpurchase" value="<?php echo html_escape($tlvmonthpurchase); ?>" name="">
<input type="hidden" id="salspurhcaselabel" value="<?php echo display(" sales_and_purchase_report_summary"); ?>">

<script src="<?php echo base_url() ?>assets/js/Chart.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/canvasjs.min.js" type="text/javascript"></script>

<script>
    function searchTrendPie() {
        var dateValue = document.getElementById("alldata").value;
        var monthMap = {
            "January": 1,
            "February": 2,
            "March": 3,
            "April": 4,
            "May": 5,
            "June": 6,
            "July": 7,
            "August": 8,
            "September": 9,
            "October": 10,
            "November": 11,
            "December": 12
        };
        var parts = dateValue.split(" ");
        var monthName = parts[0];
        var year = parts[1];
        var monthNumber = monthMap[monthName];

        $.ajax({
            url: $('#base_url').val() + 'invoice/invoice/best_of_sale2',
            type: 'POST',
            data: {
                month: monthNumber,
                year: year
            },
            success: function(response) {
                var jsonData = JSON.parse(response);

                if (jsonData === "") {
                    // Display a message or handle empty data case
                    document.getElementById("chartContainer").innerHTML = "<p>No data available for the selected period.</p>";
                } else {
                    var totalProductCount = jsonData.reduce(function(total, item) {
                        return total + parseInt(item.product_count);
                    }, 0);
                    var dataPoints = jsonData.map(function(item) {
                        var productCount = parseInt(item.product_count);
                        var percentage = ((productCount / totalProductCount) * 100).toFixed(2) / 100; // Calculate percentage
                        return {
                            y: percentage, // Set percentage as y value
                            label: item.category_name // Set product_name as label
                        };
                    });
                    var chart = new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true,
                        data: [{
                            type: "pie",
                            startAngle: 240,
                            yValueFormatString: "#0.00%", // Display percentage with two decimal points
                            indexLabel: "{label} {y}%", // Append % to label
                            dataPoints: dataPoints // Use the dynamically created dataPoints
                        }]
                    });
                    chart.render();
                }

            },
            error: function(error) {
                console.log(error)
            }
        });
    }
    $(function() {
        "use strict";

        $('#alldata').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            maxDate: "+0M",
            dateFormat: 'MM yy'
        }).focus(function() {
            var thisCalendar = $(this);
            $('.ui-datepicker-calendar').detach();
            $('.ui-datepicker-close').click(function() {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                thisCalendar.datepicker('setDate', new Date(year, month, 1));
            });
        });
    });
    window.onload = function() {
        searchTrendPie();
    }
    $(function() {

        var mvar = $("#month").val();
        var splitmonth = mvar.substring(0, mvar.length - 1);
        var month = splitmonth.split(",");

        var tmsl = $("#tlvmonthsale").val();
        var splitsale = tmsl.substring(0, tmsl.length - 1);
        var sale = splitsale.split(",");


        var tmpurchase = $("#tlvmonthpurchase").val();
        var splitpurchase = tmpurchase.substring(0, tmpurchase.length - 1);
        var purchase = splitpurchase.split(",");
        var label = $("#salspurhcaselabel").val();


        new Chart(document.getElementById("yearlyreport"), {
            type: 'line',
            data: {
                labels: month,
                datasets: [{
                    data: sale,
                    label: "Sales",
                    borderColor: "#008000",
                    fill: false
                }, {
                    data: purchase,
                    label: "Purchase",
                    borderColor: "#3e95cd",
                    fill: false
                }]
            },
            options: {
                title: {
                    display: true,
                    text: label
                }
            }
        });




        var bestslabel = $("#bestsalelabel").val();
        var splitbslabel = bestslabel.substring(0, bestslabel.length - 1);
        var bestsalelabel = splitbslabel.split(",");

        var bestsdata = $("#bestsaledata").val();
        var splitbsdata = bestsdata.substring(0, bestsdata.length - 1);
        var bestsaledata = splitbsdata.split(",");

        bestsalelabel.pop();
        bestsaledata.pop();

        var bestsalmax = $("#bestsalemax").val();
        var ctx = document.getElementById("lineChart");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: bestsalelabel,
                datasets: [{
                        label: "Sales Product",
                        backgroundColor: [
                            '#FF6384', // Color for first bar
                            '#36A2EB', // Color for second bar
                            '#FFCE56', // Color for third bar
                            '#4BC0C0', // Color for fourth bar
                            '#9966FF', // Color for fifth bar
                            '#FF9F40', // Color for sixth bar
                            '#f54733', // Color for seventh bar
                            '#71B37C', // Color for eighth bar
                            '#F45B69', // Color for ninth bar
                            '#2E86C1' // Color for tenth bar
                        ],
                        pointLabelFontSize: 30,
                        data: bestsaledata
                    }

                ]
            },
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Products'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 10,
                            stepValue: 5,
                            // max: Number(bestsalmax)
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Percentage (%)'
                        }
                    }]
                },
                animation: {
                    duration: 2000, // Animation duration in milliseconds
                    easing: 'easeOutBounce', // Animation effect
                    onComplete: function() {
                        var chartInstance = this.chart,
                            ctx = chartInstance.ctx;

                        ctx.fillStyle = '#000000'; // Text color
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        this.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                var data = dataset.data[index];
                                ctx.fillText(data, bar.x, bar.y - 5);
                            });
                        });
                    }
                }
            }


        });
    });
</script>