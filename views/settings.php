<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <h4>Angola AGT Certification Details</h4>
        <hr class="hr-10" />
        
        <div class="form-group" property="angola_saft_certification_no">
            <label for="angola_saft_certification_no" class="control-label">Software Certification Number (e.g. 000/AGT/2026)</label>
            <input type="text" name="settings[angola_saft_certification_no]" id="angola_saft_certification_no" 
                class="form-control" value="<?php echo get_option('angola_saft_certification_no'); ?>">
        </div>

        <div class="form-group" property="angola_saft_key_version">
            <label for="angola_saft_key_version" class="control-label">Signing Key Version</label>
            <input type="text" name="settings[angola_saft_key_version]" id="angola_saft_key_version" 
                class="form-control" value="<?php echo get_option('angola_saft_key_version'); ?>">
        </div>

        <div class="form-group" property="angola_saft_private_key">
            <label for="angola_saft_private_key" class="control-label">RSA Private Key (PEM format)</label>
            <textarea name="settings[angola_saft_private_key]" id="angola_saft_private_key" 
                class="form-control" rows="8"><?php echo get_option('angola_saft_private_key'); ?></textarea>
            <p class="help-block">This key is used to sign invoices. Keep it secret.</p>
        </div>

        <div class="form-group" property="angola_saft_public_key">
            <label for="angola_saft_public_key" class="control-label">RSA Public Key (PEM format)</label>
            <textarea name="settings[angola_saft_public_key]" id="angola_saft_public_key" 
                class="form-control" rows="4"><?php echo get_option('angola_saft_public_key'); ?></textarea>
        </div>

        <hr />
        <h4>AGT REST API Integration (2026 Mandatory Real-time Submission)</h4>
        <div class="form-group" property="angola_saft_api_endpoint">
            <label for="angola_saft_api_endpoint" class="control-label">AGT API Gateway URL</label>
            <input type="text" name="settings[angola_saft_api_endpoint]" id="angola_saft_api_endpoint" 
                class="form-control" value="<?php echo get_option('angola_saft_api_endpoint'); ?>">
        </div>

        <div class="form-group" property="angola_saft_api_token">
            <label for="angola_saft_api_token" class="control-label">API Access Token / Certificate ID</label>
            <input type="password" name="settings[angola_saft_api_token]" id="angola_saft_api_token" 
                class="form-control" value="<?php echo get_option('angola_saft_api_token'); ?>">
        </div>
    </div>
</div>
