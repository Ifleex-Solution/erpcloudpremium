<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div>
                <div class="panel-body print-font-size">
                    <div class="row">

                        <div class="col-xs-4">

                            <img style="width: 210px; height:79px;" src="<?php
                                                                            if (isset($currency_details[0]['invoice_logo'])) {
                                                                                echo base_url() . $currency_details[0]['invoice_logo'];
                                                                            }
                                                                            ?>" class="img-bottom-m print-logo invoice-img-position" alt="">
                            <br>
                            <span
                                class="label label-success-outline m-r-15 p-10"><?php echo display('billing_from') ?></span>
                            <address class="margin-top10">
                                <strong class=""><?php echo $company_info[0]['company_name'] ?></strong><br>
                                <span class="comp-web"><?php echo $company_info[0]['address'] ?></span><br>
                                <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                <?php echo $company_info[0]['mobile'] ?><br>
                                <abbr><b><?php echo display('email') ?>:</b></abbr>
                                <?php echo $company_info[0]['email'] ?><br>
                                <abbr><b><?php echo display('website') ?>:</b></abbr>
                                <span class="comp-web"><?php echo $company_info[0]['website'] ?></span><br>
                            </address>



                        </div>
                        <div class="col-xs-4">
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

                        <div class="col-xs-4 text-left ">
                            <h3 class="m-t-0"> <?php echo $title; ?></h3>

                            <div class="m-b-15">

                            </div>
                            <div class="m-b-15">
                                <abbr class="font-bold"> <?php echo $title1; ?></abbr>
                                <?php echo $invoiceno; ?>
                                <br>
                                <abbr class="font-bold">Date:</abbr>
                                <?php echo date("d-M-Y", strtotime($invoice_all_data[0]['date'])); ?>
                                <br />
                                <abbr class="font-bold">Store:</abbr>
                                <?php echo $invoice_all_data[0]['store_name']; ?>
                                <br>
                                <abbr class="font-bold">Type:</abbr>
                                <?php echo $invoice_all_data[0]['type_name']; ?>
                                <br>
                                <abbr class="font-bold">Voucher No:</abbr>
                                <?php echo $invoice_all_data[0]['voucher_no']; ?>
                                <br>
                                <abbr class="font-bold">Vehicle No:</abbr>
                                <?php echo $invoice_all_data[0]['vehicleno']; ?>
                                <br>
                            </div>
                            <span class="label label-success-outline m-r-15"><?php echo display('billing_to') ?></span>

                            <address style="margin-top: 10px;" class="">
                                <strong class=""><?php echo $name ?> </strong><br>
                                <?php if ($address) { ?>
                                    <?php echo $address; ?>
                                    <br>
                                <?php } ?>
                                <?php if ($mobile) { ?>
                                    <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                    <?php echo $mobile; ?>
                                    <br>
                                <?php }  ?>
                            </address>


                        </div>
                    </div>

                    <br />

                    <table class="invoice-details" width="95%;">
                        <tr>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">SL.</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Product Name</th>
                            <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Unit</th>
                            <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Qty</th>
                        </tr>
                        <?php
                        $sl = 1;
                        $total_qty = 0;

                        foreach ($invoice_all_data as $invoice_data) { ?>

                            <tr>
                                <td style="padding: 5px;width: 200px;"><?php echo $sl; ?></td>
                                <td style="width: 300px;font-size: 13px;padding: 5px;"><?php echo $invoice_data['product_name']; ?></td>
                                <td style="width: 300px;font-size: 13px;padding: 5px;"><?php echo $invoice_data['unit']; ?></td>
                                <td style="font-size: 14px;padding: 5px;text-align: center;"><?php
                                                                                                $quantity = abs($invoice_data['quantity']);
                                                                                                echo $quantity; ?></td>

                            </tr>
                        <?php
                            // $dis = is_numeric($invoice_data['discount_per']) ? $invoice_data['discount_per'] : 0;
                            // $total_discount = $total_discount + (float)$dis;
                            $total_qty = $total_qty + (float)abs($invoice_data['quantity']);
                            $sl++;
                        } ?>

                        <tr>
                            <td colspan="3" style="text-align: left; border-top: 2px solid;font-size: 14px;padding: 5px; "><b>Total Qty:</b></td>

                            <td style="border-top: 2px solid;font-size: 14px;padding: 5px;text-align: center; "> <?php echo $total_qty; ?> </td>

                        </tr>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>