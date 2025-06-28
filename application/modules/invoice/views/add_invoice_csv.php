<script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.2/papaparse.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <!-- Multiple panels with drag & drop -->
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('csv_file_informaion') ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <a href="<?php echo base_url('assets/data/csv/sample_invoice.csv') ?>" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample File</a>
                <span class="text-warning">The first line in downloaded csv file should remain as it is. Please do not change the order of columns.</span><br>The correct column order is <span class="text-info">
                    (Invoice ID,Sale Date, Branch, Incident Type,Customer ,Employee,Product,Store,Unit,Qty,Price Val,Discount,VAT,Sale Discount,Payment Type,Details )</span> &amp; you must follow this.<br>Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).<p>The images should be uploaded in <strong>uploads</strong> folder.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Import Invoice CSV</h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="upload_csv_file" class="col-sm-4 col-form-label"><?php echo display('upload_csv_file') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="upload_csv_file" type="file" id="csvFile" placeholder="<?php echo display('upload_csv_file') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6">
                        <input type="button" onclick="uploadCSV()" id="save_add" class="btn btn-primary btn-large" name="add-product" value="<?php echo display('submit') ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Uploaded Invoice Details</h4>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="stockdisposalnote">
                    <thead>
                        <tr>
                            <th><?php echo display('sl') ?></th>
                            <th>Uploaded Id</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            <th><?php echo display('action') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<?php
echo "<script>";
echo "let pmethods=" . json_encode($pmetods) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($stores) . ";";
echo "let customers=" . json_encode($customers) . ";";
echo "let employees=" . json_encode($employees) . ";";
echo "let branches=" . json_encode($branches) . ";";
echo "let invoicesdetails=" . json_encode($invoicesdetails) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>

<script>
    function uploadCSV() {
        console.log(invoicesdetails)
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a CSV file.');
            return;
        }
        $("#save_add").hide();


        Papa.parse(file, {
            header: true, // parses first row as header
            transformHeader: (header) => header.replace(/\s+/g, ''),
            complete: function(results) {
                // Send data to the server
                let uploadeddata = results.data;

                let rownum = 2;
                uploadeddata.sort((a, b) => {
                    return (a.InvoiceID || '').localeCompare(b.InvoiceID || '');
                });
                let invoiceId = "";
                let tdis = 0;
                let tvat = 0;
                let ttotalprice = 0;
                let grandtotal = 0;
                let saleDiscount = 0;
                arrInvoice = [];
                arrItem = [];
                for (const item of uploadeddata) {
                    if (item.InvoiceID != "") {
                        if (invoicesdetails) {
                            invoicecheck = invoicesdetails.find(invoice => invoice.sale_id.toLowerCase() === item.InvoiceID.toLowerCase());
                            if (invoicecheck != undefined) {
                                alert("Invoice ID Already exsist: " + item.InvoiceID + "\nRow Number: " + rownum + "\nColumn: Invoice");
                                $("#save_add").show();
                                return
                            }
                        }

                        let storecheck = null;

                        if (stores) {
                            storecheck = stores.find(store => store.name.toLowerCase() === item.Store.toLowerCase());
                            if (storecheck == undefined) {
                                alert("Cannot find Store: " + item.Store + "\nRow Number: " + rownum + "\nColumn: Store");
                                $("#save_add").show();
                                return
                            }
                        } else {
                            alert("Cannot find Store: " + item.Store + "\nRow Number: " + rownum + "\nColumn: Store");
                            $("#save_add").show();
                            return
                        }

                        let productcheck = null;


                        if (products) {
                            productcheck = products.find(product => product.product_name.toLowerCase() === item.Product.toLowerCase());
                            if (productcheck == undefined) {
                                alert("Cannot find Product: " + item.Product + "\nRow Number: " + rownum + "\nColumn: Product");
                                $("#save_add").show();
                                return
                            }
                        } else {
                            alert("Cannot find Product: " + item.Product + "\nRow Number: " + rownum + "\nColumn: Product");
                            $("#save_add").show();
                            return
                        }


                        let paymentcheck = null;

                        if (pmethods) {
                            paymentcheck = pmethods.find(pmethod => pmethod.name.toLowerCase() === item.PaymentType.toLowerCase());
                            if (paymentcheck == undefined) {
                                alert("Cannot find Payment Type: " + item.PaymentType + "\nRow Number: " + rownum + "\nColumn: Payment Type");
                                $("#save_add").show();
                                return
                            }
                        } else {
                            alert("Cannot find Payment Type: " + item.PaymentType + "\nRow Number: " + rownum + "\nColumn: Payment Type");
                            $("#save_add").show();
                            return
                        }


                        incidentTypeId = 0;
                        if (item.IncidentType.toLowerCase() == 'sale') {
                            incidentTypeId = 1;
                        } else if (item.IncidentType.toLowerCase() == 'wholesale') {
                            incidentTypeId = 2;
                        } else {
                            alert("Cannot find Incident Type: " + item.IncidentType + "\nRow Number: " + rownum + "\nColumn: Incident Type");
                            $("#save_add").show();
                            return
                        }
                        let branchcheck = null;

                        if (branches) {
                            branchcheck = branches.find(branch => branch.name.toLowerCase() === item.Branch.toLowerCase());
                            if (branchcheck == undefined) {
                                alert("Cannot find Branch: " + item.Branch + "\nRow Number: " + rownum + "\nColumn: Branch");
                                $("#save_add").show();
                                return
                            }
                        } else {
                            alert("Cannot find Branch: " + item.Branch + "\nRow Number: " + rownum + "\nColumn: Branch");
                            $("#save_add").show();
                            return
                        }


                        let employeecheck = null

                        if (item.Employee != '') {
                            if (employees) {
                                employeecheck = employees.find(employee => employee.last_name.toLowerCase() === item.Employee.toLowerCase());
                                if (employeecheck == undefined) {
                                    alert("Cannot find Employee: " + item.Employee + "\nRow Number: " + rownum + "\nColumn: Employee");
                                    $("#save_add").show();
                                    return
                                }
                            } else {
                                alert("Cannot find Employee: " + item.Employee + "\nRow Number: " + rownum + "\nColumn: Employee");
                                $("#save_add").show();
                                return
                            }
                        }
                        let customercheck = customers.find(customer => customer.customer_name.toLowerCase() === item.Customer.toLowerCase());
                        if (customercheck == undefined) {
                            alert("Cannot find Customer: " + item.Customer + "\nRow Number: " + rownum + "\nColumn: Customer");
                            $("#save_add").show();
                            return
                        }

                        // let price = parseFloat(item.PriceVal.replace(/,/g, ''));
                        if (invoiceId != item.InvoiceID) {
                            invoiceId = item.InvoiceID;
                            grandtotal = ttotalprice - parseFloat(saleDiscount) + tvat;
                            tdis = parseFloat(saleDiscount) + tdis
                            if (arrInvoice.length > 0) {
                                arrInvoice[arrInvoice.length - 1].total_discount_ammount = tdis;
                                arrInvoice[arrInvoice.length - 1].total_vat_amnt = tvat;
                                arrInvoice[arrInvoice.length - 1].total = ttotalprice;
                                arrInvoice[arrInvoice.length - 1].grandTotal = grandtotal;
                                arrInvoice[arrInvoice.length - 1].discount = parseFloat(saleDiscount)

                            }

                            arrInvoice.push({
                                'invoiceId': invoiceId,
                                'total_discount_ammount': 0,
                                'total_vat_amnt': 0,
                                'branch': branchcheck.id,
                                'payment': paymentcheck.id,
                                'customer_id': customercheck.customer_id,
                                'employee_id': employeecheck != null ? employeecheck.id : null,
                                'date': item.SaleDate,
                                'details': item.Details,
                                'incidenttype': incidentTypeId,
                                'grandTotal': 0,
                                'total': 0,
                                'discount': 0,
                                'type2': type2

                            });
                            

                            tdis = 0;
                            tvat = 0;
                            ttotalprice = 0;
                            saleDiscount = 0;
                            grandtotal = 0;
                            if (item.SaleDiscount != "") {
                                saleDiscount = parseFloat(item.SaleDiscount.replace(/,/g, ''));
                                console.log(saleDiscount)
                            }
                        }
                        let price = 0;
                        let disc = 0;
                        let vat = 0;
                        let totalprice = 0;

                        try {

                            if (item.PriceVal == '') {
                                alert("Input value in Price Val is empty \nRow Number: " + rownum);
                                $("#save_add").show();
                                return
                            }

                            if (item.Qty == '') {
                                alert("Input value in Qty is empty \nRow Number: " + rownum);
                                $("#save_add").show();
                                return
                            }

                            price = parseFloat(item.Qty.replace(/,/g, '')) * parseFloat(item.PriceVal.replace(/,/g, ''));
                            disc = 0;
                            if (item.Discount != '') {
                                disc = +(price * parseFloat(item.Discount.replace(/,/g, '')) / 100);
                                tdis = tdis + disc;
                            }
                            totalprice = price - disc;


                            vat = 0;
                            if (item.VAT != "") {
                                vat = +(totalprice * parseFloat(item.VAT.replace(/,/g, '')) / 100);
                                tvat = vat + tvat;
                            }

                            ttotalprice = ttotalprice + totalprice;
                            arrItem.push({
                                invoiceId: item.InvoiceID,
                                product: productcheck.id,
                                store: storecheck.id,
                                quantity: item.Qty,
                                product_rate: parseFloat(item.PriceVal.replace(/,/g, '')),
                                discount: parseFloat(item.Discount.replace(/,/g, '')),
                                discount_value: disc,
                                vat_percent: parseFloat(item.VAT.replace(/,/g, '')),
                                vat_value: vat,
                                total_price: totalprice,
                                total_discount: 0,
                                all_discount: 0,
                            });
                            if (isNaN(price) || isNaN(disc) || isNaN(vat)) {
                                alert("Input value in Price Val, Qty, VAT, or Discount is not valid \nRow Number: " + rownum);
                                $("#save_add").show();
                                return
                            }

                        } catch (error) {
                            alert("Input value in Price Val,Qty,VAT,Discount not valid \nRow Number: " + rownum)
                            $("#save_add").show();
                            return
                        }
                        rownum = rownum + 1;
                    }




                }

                if (arrInvoice.length > 0) {
                    grandtotal = ttotalprice - parseFloat(saleDiscount) + tvat;
                    tdis = parseFloat(saleDiscount) + tdis
                    arrInvoice[arrInvoice.length - 1].total_discount_ammount = tdis;
                    arrInvoice[arrInvoice.length - 1].total_vat_amnt = tvat;
                    arrInvoice[arrInvoice.length - 1].total = ttotalprice;
                    arrInvoice[arrInvoice.length - 1].grandTotal = grandtotal;
                    arrInvoice[arrInvoice.length - 1].discount = parseFloat(saleDiscount)
                }

                $.ajax({
                    url: $('#base_url').val() + 'invoice/invoice/save_sale2',
                    type: 'POST',
                    data: {
                        items: arrItem,
                        invoice: arrInvoice,
                    },
                    success: function(response) {
                        datas = JSON.parse(response);
                        // clearDetails()
                        $("#save_add").show();

                        alert("Invoice Details saved Successfully")
                        location.reload();
                        // printRawHtml(datas.details);
                    },
                    error: function(error) {
                        console.log(error)
                    }
                });
            }
        });
    }
    let type2 = '';
    $(document).ready(function() {
        if (usertype == 3) {
            // document.getElementById('style12').style.backgroundColor = '#E0E0E0';
            // const title = document.getElementById('title');
            // title.style.color = 'blue';
            type2 = "B"

        } else {
            type2 = "A"

        }

        var csrf_test_name = $('#CSRF_TOKEN').val();
        var base_url = $('#base_url').val();

        $('#stockdisposalnote').DataTable({
            responsive: true,

            "aaSorting": [
                [1, "desc"]
            ],
            "columnDefs": [{
                    "bSortable": false,
                    "aTargets": [0, 1, 2, 3]
                },

            ],
            'processing': true,
            'serverSide': true,


            'lengthMenu': [
                [10, 25, 50, 100, 250, 500, 1000],
                [10, 25, 50, 100, 250, 500, 1000]
            ],

            dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: [{
                extend: "copy",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want
                },
                className: "btn-sm prints"
            }, {
                extend: "csv",
                title: "Manage Sale",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                className: "btn-sm prints"
            }, {
                extend: "excel",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "Manage Sale",
                className: "btn-sm prints"
            }, {
                extend: "pdf",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "Manage Sale",
                className: "btn-sm prints"
            }, {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "<center>Manage Sale</center>",
                className: "btn-sm prints"
            }],

            'serverMethod': 'post',
            'ajax': {
                'url': base_url + 'invoice/invoice/checkBulkUpload',
                data: {
                    csrf_test_name: csrf_test_name
                }
            },
            'columns': [{
                    data: 'sl'
                },

                {
                    data: 'uploaded_id'
                },
                {
                    data: 'date'
                },
                {
                    data: 'name'
                },
                {
                    data: 'button'
                },
            ],

        });
    });

  

    function exportToExcel(invoices) {
        if (confirm("Do you want to download this record")) {
            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'invoice/invoice/download_bulk',
                data: {
                    invoices: invoices.split(","),
                },
                success: function(data1) {
                   

                   datas = JSON.parse(data1);

                   console.log(datas)


                    const ws =  XLSX.utils.json_to_sheet(datas);

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

                    XLSX.writeFile(wb, "data_export.csv");

                }
            });
        }

    }

     function deletesale(invoices,id) {
        if (confirm("Do you want to delete this record")) {
            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'invoice/invoice/delete',
                data: {
                    id:id,
                    invoices: invoices.split(","),
                },
                success: function(data1) {
                   
                     alert("Deleted successfully") 
                     location.reload();
                }
            });
        }

    }

    
</script>