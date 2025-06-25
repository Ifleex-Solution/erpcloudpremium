<div class="row">
    <div class="col-sm-12">
        <?php if ($this->permission1->method('add_purchase', 'create')->access()) { ?>
            <a href="<?php echo base_url('add_purchase') ?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-plus"> </i>
                <?php echo display('add_purchase') ?> </a>
        <?php } ?>
        <?php if ($this->permission1->method('manage_supplier', 'read')->access()) { ?>
            <a href="<?php echo base_url('purchase_list') ?>" class="btn btn-info m-b-5 m-r-2"><i class="ti-align-justify">
                </i> <?php echo display('manage_purchase') ?> </a>
        <?php } ?>


    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('purchase_details') ?></span>
                    <span class="print-button">
                        <button class="btn btn-info " onclick="printDivnew('printableArea')"><span
                                class="fa fa-print"></span></button></span>
                </div>
            </div>
            <div class="panel-body " id="printableArea">

                <div class="row print-font-size">

                    <div class="col-xs-4 ">
                        <?php foreach ($company_info as $cominfo) { ?>
                            <img style="width: 210px; height:79px;" src="<?php
                                                                            if (isset($setting->invoice_logo)) {
                                                                                echo base_url() . $setting->invoice_logo;
                                                                            }
                                                                            ?>" class="img-bottom-m print-logo invoice-img-position" alt="">
                            <br>
                            <br>

                            <span
                                class="label label-success-outline m-r-15 p-10"><?php echo display('billing_from') ?></span>
                            <address class="margin-top10">
                                <strong class=""><?php echo $company_info12[0]['company_name'] ?></strong><br>
                                <span class="comp-web"><?php echo $company_info12[0]['address'] ?></span><br>
                                <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                <?php echo $company_info12[0]['mobile'] ?> <br>

                                <abbr class="font-bold"><?php echo display('email') ?>:</abbr>
                                <?php echo $company_info12[0]['email'] ?> <br>
                                <abbr class="font-bold"><?php echo display('website') ?>:</abbr>
                                <span class="comp-web"><?php echo $company_info12[0]['website'] ?></span><br>

                            <?php } ?>

                            </address>



                    </div>
                    <?php $create_at = $this->db->select('CreateDate')->from('acc_vaucher')->where('referenceNo', $purchase_id)->get()->row(); ?>
                    <div class="col-xs-5">
                        <?php $web_setting = $this->db->select("*")->from("web_setting")->get()->row();
                        if ($web_setting->is_qr == 1) { ?>
                            <div class="print-qr">
                                <?php $text = base64_encode(display('invoice_no') . ': ' . $invoice_no . ' ' . display('customer_name') . ': ' . $customer_name);
                                ?>
                                <img src="http://chart.apis.google.com/chart?cht=qr&chs=250x250&chld=L|4&chl=<?php echo $text ?>"
                                    alt="">
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-xs-3 text-left ">
                        <h2 class="m-t-0"><?php echo display('purchase') ?></h2>
                        <div>
                            <abbr class="font-bold">
                            <?php echo display('invoice_no') ?>:
                            </abbr>
                            <?php echo $chalan_no; ?>
                        </div>
                        <div class="m-b-15">
                            <abbr class="font-bold"><?php echo display('billing_date') ?>: </abbr>
                            <?php echo date("d-M-Y", strtotime($date)); ?>
                            <br /> <br />


                            <span class="label label-success-outline m-r-15"><?php echo display('billing_to') ?></span>
                            <address style="margin-top: 10px;" class="">
                                <strong class=""><?php echo $supplier_name ?> </strong><br>
                                <?php if ($address) { ?>
                                    <?php echo $address; ?>
                                    <br>
                                <?php } ?>
                                <?php if ($mobile) { ?>
                                    <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                    <?php echo $mobile; ?>
                                    <br>
                                <?php } ?>
                                <?php if ($emailnumber) { ?>
                                    <abbr class="font-bold"><?php echo display('email') ?>: </abbr>
                                    <?php echo $emailnumber; ?>
                                    <br>
                                <?php } ?>



                            </address>

                        </div>
                    </div>



                </div>

                <br />

                <table class="invoice-details" width="100%;">
                    <tr>
                        <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">SL.</th>
                        <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Product Name</th>
                        <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;text-align: right;">Qty</th>
                        <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Rate</th>
                        <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 10px;font-size: 14px;text-align: right;">Dis. Value</th>
                        <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px;font-size: 14px;text-align: right;">VAT Value</th>
                        <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px;font-size: 14px;text-align: right;">Total</th>
                    </tr>
                    <?php
                    $sl = 1;
                    $total_qty = 0;

                    foreach ($invoice_all_data as $invoice_data) { ?>

                        <tr>
                            <td style="padding: 10px;"><?php echo $sl; ?></td>
                            <td style="width: 300px;font-size: 13px;padding: 5px;"><?php echo $invoice_data['product_name']; ?></td>
                            <td style="font-size: 14px;padding: 5px;text-align: right;text-align: right;"><?php echo $invoice_data['quantity']; ?></td>
                            <td style="width: 200px; text-align: right;font-size: 14px;padding: 5px;text-align: right;"><?php echo number_format($invoice_data['product_rate'], 2, '.', ','); ?></td>
                            <td style="width: 150px; text-align: right;font-size: 14px;padding: 5px;text-align: right;"><?php echo number_format($invoice_data['discount_value'], 2, '.', ','); ?> </td>
                            <td style="width: 150px; text-align: right;font-size: 14px;padding: 5px;text-align: right;"> <?php echo number_format($invoice_data['vat_value'], 2, '.', ','); ?></td>
                            <td style="width: 200px; text-align: right;font-size: 14px;padding: 5px;text-align: right;"> <?php echo number_format($invoice_data['total_price'], 2, '.', ','); ?></td>
                        </tr>
                    <?php
                        // $dis = is_numeric($invoice_data['discount_per']) ? $invoice_data['discount_per'] : 0;
                        // $total_discount = $total_discount + (float)$dis;
                        $total_qty = $total_qty + (float)$invoice_data['quantity'];
                        $sl++;
                    } ?>

                    <tr>
                        <td colspan="2" style="text-align: right;border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px; "><b>Total Qty:</b></td>

                        <td style="border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px;text-align: right; "> <?php echo $total_qty; ?> </td>
                        <td colspan="3" style="text-align: right;border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px; "><b>Total:</b></td>

                        <td style="text-align: right;border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px; "> <?php echo number_format($total, 2, '.', ','); ?> </td>


                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right;font-size: 14px;padding: 5px; "><b>Purchase Discount:</b></td>

                        <td style="text-align: right;font-size: 14px;padding: 5px; "> <?php echo number_format($total_dis, 2, '.', ','); ?></td>

                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right;font-size: 14px;padding: 5px; "><b>Total Discount Amount:</b></td>

                        <td style="text-align: right;font-size: 14px;padding: 5px; "> <?php echo number_format($total_discount_ammount, 2, '.', ','); ?></td>

                    </tr>

                    <tr>
                        <td colspan="6" style="text-align: right;font-size: 14px;padding: 5px; "><b>Total VAT Amount:</b></td>

                        <td style="text-align: right;font-size: 14px;padding: 5px; "> <?php echo number_format($total_vat_amnt, 2, '.', ','); ?></td>

                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right;font: size 20px;padding: 5px;"><b>Grand Total:</b></td>

                        <td style="text-align: right;font: size 20px;padding: 5px; "> <?php echo number_format($grandTotal, 2, '.', ','); ?></td>

                    </tr>
                </table>




            </div>
        </div>
    </div>
</div>