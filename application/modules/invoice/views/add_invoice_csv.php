<script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.2/papaparse.min.js"></script>

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
                <a href="<?php echo base_url('assets/data/csv/sample_product .csv') ?>" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample File</a>
                <span class="text-warning">The first line in downloaded csv file should remain as it is. Please do not change the order of columns.</span><br>The correct column order is <span class="text-info">(Serial No,Supplier Name, Product Name, Product Model,Category Name ,Sale Price,Supplier Price Product Variants separated by vertical bar)</span> &amp; you must follow this.<br>Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).<p>The images should be uploaded in <strong>uploads</strong> folder.</p>
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
                        <input type="button" onclick="uploadCSV()" id="add-product" class="btn btn-primary btn-large" name="add-product" value="<?php echo display('submit') ?>" />
                    </div>
                </div>
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
echo "</script>";
?>

<script>
    function uploadCSV() {
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a CSV file.');
            return;
        }

        Papa.parse(file, {
            header: true, // parses first row as header
            transformHeader: (header) => header.replace(/\s+/g, ''),
            complete: function(results) {
                // Send data to the server
                console.log(results.data)
                let uploadeddata = results.data;

                let rownum = 2;
                for (const item of uploadeddata) {
                    if (item.InvoiceID != "") {
                        let branchcheck = branches.find(branch => branch.name.toLowerCase() === item.Branch.toLowerCase());
                        if (branchcheck == undefined) {
                            alert("Cannot find Branch: " + item.Branch + "\nRow Number: " + rownum + "\nColumn: Branch");
                            return
                        }

                        let employeecheck = null

                        if (item.Employee != '') {
                            employeecheck = employees.find(employee => employee.last_name.toLowerCase() === item.Employee.toLowerCase());
                            if (employeecheck == undefined) {
                                alert("Cannot find Employee: " + item.Employee + "\nRow Number: " + rownum + "\nColumn: Employee");
                                return
                            }
                        }
                        let customercheck = customers.find(customer => customer.customer_name.toLowerCase() === item.Customer.toLowerCase());
                        if (customercheck == undefined) {
                            alert("Cannot find Customer: " + item.Customer + "\nRow Number: " + rownum + "\nColumn: Customer");
                            return
                        }
                        let storecheck = stores.find(store => store.name.toLowerCase() === item.Store.toLowerCase());
                        console.log(storecheck)
                        if (storecheck == undefined) {
                            alert("Cannot find Store: " + item.Store + "\nRow Number: " + rownum + "\nColumn: Store");
                            return
                        }


                        let productcheck = products.find(product => product.product_name.toLowerCase() === item.Product.toLowerCase());
                        if (productcheck == undefined) {
                            alert("Cannot find Product: " + item.Product + "\nRow Number: " + rownum + "\nColumn: Product");
                            return
                        }

                         let paymentcheck = pmethods.find(pmethod => pmethod.name.toLowerCase() === item.PaymentType.toLowerCase());
                        if (paymentcheck == undefined) {
                            alert("Cannot find Payment Type: " + item.PaymentType + "\nRow Number: " + rownum + "\nColumn: Payment Type");
                            return
                        }



                        rownum = rownum + 1;
                    }



                }


            }
        });
    }
</script>