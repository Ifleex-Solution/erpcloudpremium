<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-bd lobidrag">
			<div class="panel-heading" id="style12">
				<div class="panel-title">
					<span id="title"><?php echo display('manage_invoice') ?></span>
					<span class="padding-lefttitle">
						<table>
							<tr>
								<td><span><b>Branch</span></b></td>
								<td style="width: 250px;padding-left: 20px;">
									<select class="form-control " id="branch" name="branch" tabindex="3" style="width: 400px;" onchange="getBranchDropdown(this.value)">
										<!-- options go here -->
									</select>
								</td>
								<td style="padding-left: 20px;">
									<?php if ($this->permission1->method('manage_invoice', 'create')->access()) { ?>
										<a href="<?php echo base_url('add_invoice') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_invoice') ?> </a>
									<?php } ?>
								</td>
							</tr>
						</table>



					</span>

				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="stockdisposalnote">
						<thead>
							<tr>
								<th><?php echo display('sl') ?></th>
								<th>Invoice No</th>
								<th>Customer</th>
								<th>Date</th>
								<th>Payment Type</th>
								<th>Details</th>
								<th>Incident Type</th>
								<th>Total Amount</th>
								<th><?php echo display('action') ?>
								</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
						<tfoot>
							<th colspan="7" class="text-right"><?php echo display('total') ?>:</th>

							<th></th>
							<th></th>
						</tfoot>
					</table>

				</div>
			</div>
		</div>
		<input type="hidden" id="total_product" value="<?php echo $total_product; ?>" name="">
	</div>
</div>
<?php
echo "<script>";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>
<script>
	let type2 = ""

	$(document).ready(function() {
		if (usertype == 3) {
			document.getElementById('style12').style.backgroundColor = '#E0E0E0';
			const title = document.getElementById('title');
			title.style.color = 'blue';
			type2 = "B"

		} else {
			type2 = "A"

		}

		getBranchDropdown(0);
	});

	function reprintInvoice(invoiceId) {
		if (confirm("Do you want to reprint this record")) {
			$.ajax({
				type: "post",
				url: $('#base_url').val() + 'invoice/invoice/pos_print',
				data: {
					id: invoiceId,

				},
				success: function(data1) {
					datas = JSON.parse(data1);
					printRawHtml(datas.details, invoiceId);

				}
			});
		}

	}

	function getBranchDropdown(branchId) {

		if ($.fn.DataTable.isDataTable('#stockdisposalnote')) {
			$('#stockdisposalnote').DataTable().clear().destroy();
		}

		var base_url = $('#base_url').val();

		$.ajax({
			type: "post",
			url: base_url + "store/store/getbranchbyuserid",
			data: {
				// is_credit_edit: is_credit_edit,
				// csrf_test_name: csrf_test_name
			},
			success: function(data) {
				var branches = JSON.parse(data);
				console.log(branches)
				var $branchDropdown = $('#branch');
				$branchDropdown.empty();
				$branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

				$.each(branches, function(index, branch) {
					$branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
					if (branchId == 0) {
						if (branch.default != 0) {
							$branchDropdown.val(branch.id)
							branchId = branch.id;
						}
					}

				});

				if (branchId > 0) {
					{
						$branchDropdown.val(branchId)
					}
				}

				"use strict";
				var csrf_test_name = $('#CSRF_TOKEN').val();
				var base_url = $('#base_url').val();
				var total_product = $("#total_product").val();
				$('#stockdisposalnote').DataTable({
					responsive: true,

					"aaSorting": [
						[1, "desc"]
					],
					"columnDefs": [{
							"bSortable": false,
							"aTargets": [0, 1, 2, 3, 4]
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
							columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want
						},
						className: "btn-sm prints"
					}, {
						extend: "csv",
						title: "Manage Sale",
						exportOptions: {
							columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
						},
						className: "btn-sm prints"
					}, {
						extend: "excel",
						exportOptions: {
							columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
						},
						title: "Manage Sale",
						className: "btn-sm prints"
					}, {
						extend: "pdf",
						exportOptions: {
							columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
						},
						title: "Manage Sale",
						className: "btn-sm prints"
					}, {
						extend: "print",
						exportOptions: {
							columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
						},
						title: "<center>Manage Sale</center>",
						className: "btn-sm prints"
					}],

					'serverMethod': 'post',
					'ajax': {
						'url': base_url + 'invoice/invoice/checksales',
						data: {
							csrf_test_name: csrf_test_name,
							type2: type2,
							branchid: branchId
						}
					},
					'columns': [{
							data: 'sl'
						},

						{
							data: 'id'
						},
						{
							data: 'customer_name'
						},
						{
							data: 'date'
						},
						{
							data: 'paymenttype'
						},
						{
							data: 'details'
						},
						{
							data: 'incidenttype'
						},
						// {
						// 	data: 'grandTotal'
						{
							data: 'grandTotal',
							class: "total_sale text-right",
							render: $.fn.dataTable.render.number(',', '.', 2)
						},

						// },
						{
							data: 'button'
						},
					],
					"footerCallback": function(row, data, start, end, display) {
						var api = this.api();
						api.columns('.total_sale', {
							page: 'current'
						}).every(function() {
							var sum = this
								.data()
								.reduce(function(a, b) {
									var x = parseFloat(a) || 0;
									var y = parseFloat(b) || 0;
									return x + y;
								}, 0);
							$(this.footer()).html(' ' + sum.toLocaleString(undefined, {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							}));
						});
					}

				});



			}
		});
	}


	function printRawHtml(view, invoiceId) {
		$(view).print({

			deferred: $.Deferred().done(function() {
				window.location.reload(true);
			})
		});
	}
</script>