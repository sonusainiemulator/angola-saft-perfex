<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Global SAF-T AO Export</h4>
                        <hr class="hr-panel-heading" />
                        <?php echo form_open(admin_url('angola_saft/export/generate'), ['method' => 'GET']); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_date_input('from', 'From Date', _d(date('Y-m-01'))); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_date_input('to', 'To Date', _d(date('Y-m-t'))); ?>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-info mtop25">Generate SAF-T AO XML</button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
