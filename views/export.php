<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">Angola SAF-T AO Export</h4>
                <?php echo form_open(admin_url('angola_saft/export/generate'), ['id' => 'saft-export-form', 'method' => 'GET']); ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Export Type -->
                                <div class="form-group select-placeholder">
                                    <label for="export_type">Export Type</label>
                                    <select name="export_type" id="export_type" class="selectpicker" data-width="100%">
                                        <option value="invoice" selected>Invoices</option>
                                        <option value="credit_note">Credit Notes</option>
                                    </select>
                                </div>

                                <!-- Status Filters for Invoices -->
                                <div class="form-group shifter invoices_shifter">
                                    <label>Status</label>
                                    <div class="radio radio-primary">
                                        <input type="radio" id="inv_all" value="all" checked name="status">
                                        <label for="inv_all">All</label>
                                    </div>
                                    <?php foreach($invoiceStatuses as $status){ ?>
                                        <div class="radio radio-primary">
                                            <input type="radio" id="inv_<?php echo $status; ?>" value="<?php echo $status; ?>" name="status">
                                            <label for="inv_<?php echo $status; ?>"><?php echo format_invoice_status($status, '', false); ?></label>
                                        </div>
                                    <?php } ?>
                                </div>

                                <!-- Status Filters for Credit Notes (Hidden by default) -->
                                <div class="form-group hide shifter credit_note_shifter">
                                    <label>Status</label>
                                    <div class="radio radio-primary">
                                        <input type="radio" id="cn_all" value="all" checked name="cn_status">
                                        <label for="cn_all">All</label>
                                    </div>
                                    <?php foreach($creditNoteStatuses as $status){ ?>
                                        <div class="radio radio-primary">
                                            <input type="radio" id="cn_<?php echo $status['id']; ?>" value="<?php echo $status['id']; ?>" name="cn_status">
                                            <label for="cn_<?php echo $status['id']; ?>"><?php echo $status['name']; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>

                                <!-- Period -->
                                <div class="form-group select-placeholder">
                                    <label for="period">Period</label>
                                    <select class="selectpicker" name="period" id="period" data-width="100%">
                                        <option value="all_time">All Time</option>
                                        <option value="this_month">This Month</option>
                                        <option value="last_month">Last Month</option>
                                        <option value="this_year">This Year</option>
                                        <option value="last_year">Last Year</option>
                                        <option value="custom">Custom Date Range</option>
                                    </select>
                                </div>

                                <!-- Custom Date Range -->
                                <div id="date-range" class="hide mbot15">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php echo render_date_input('from', 'From Date'); ?>
                                        </div>
                                        <div class="col-md-12">
                                            <?php echo render_date_input('to', 'To Date'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button class="btn btn-primary" type="submit">Generate SAF-T AO XML</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        // Handle Period changes
        $('#period').on('change', function() {
            var val = $(this).val();
            if (val == 'custom') {
                $('#date-range').removeClass('hide');
            } else {
                $('#date-range').addClass('hide');
            }
        });

        // Handle Export Type changes
        $('#export_type').on('change', function() {
            var val = $(this).val();
            $('.shifter').addClass('hide');
            $('.' + val + '_shifter').removeClass('hide');
        });
    });
</script>
