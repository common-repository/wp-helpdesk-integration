<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$config = get_option("smack_whi_{$skinnyData['activatedplugin']}1_settings");

	if( $config == "" )
	{
        	$config_data = 'no';
	}
	else
	{
        	$config_data = 'yes';
	}
	
	$siteurl = site_url();
	$help_img = esc_url(WP_HELPDESK_INTEGRATION_DIR) . "images/help.png";
	$callout_img = esc_url(WP_HELPDESK_INTEGRATION_DIR) . "images/callout.gif";
	$help="<img src='$help_img'>";
	$call="<img src='$callout_img'>";
	update_option("smack_whi_{$skinnyData['activatedplugin']}_settings" , $config );

?>
<input type="hidden" id="get_config" value="<?php echo esc_attr__($config_data) ?>" >
<span id="save_config" style="font:14px;width:200px;"> </span>

<span id="Fieldnames" style="font-size: 14px;font-weight:bold;float:right;padding-right:10px;padding-top:12px;padding-left:12px;"></span>
<script>
        jQuery( document ).ready( function( ) {
                jQuery( "#Fieldnames" ).hide(  );
        });

</script>

<input type="hidden" id="get_config" value="<?php echo esc_attr__($config_data) ?>" >
<input type="hidden" id="revert_old_crm_pro" value="zohodesk">
 <form id="smack-zohodesk-settings-form"  action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">

	<input type="hidden" name="smack-zohodesk-settings-form" value="smack-zohodesk-settings-form" />
	<input type="hidden" id="plug_URL" value="<?php echo esc_url(WP_HELPDESK_INTEGRATION_PLUG_URL);?>" />
	<div class="wp-common-crm-content" style="width:100%;float: left;">
	<table style="width: 40%;">
		<tr>
			<td><label id="inneroptions" style="margin-top:6px;margin-right:4px;font-weight:bold"><?php echo esc_html__("Select the HelpDesk you use", WP_HELPDESK_INTEGRATION_SLUG ); ?></label></td>
			<td>
			<?php
				$ContactFormPluginsObj = new SmackHelpDeskUIHelper();
				echo $ContactFormPluginsObj->getPluginActivationHtml();
			?>
			</td>
			<tr><td> <br /></tr></td>
		</tr>
    </table>
	<label id="inneroptions" style="font-weight:bold;">ZohoDesk Settings:</label>
        <table class="settings-table">

                <tr><td></td></tr>
	
		<tr>
			<td  style='width:250px;padding-left:40px;'>
				<label id="innertext"> <?php echo esc_html__('Client ID', WP_HELPDESK_INTEGRATION_SLUG); ?> </label><div style='float:right;'> : </div>
			</td>
			<td>
				<input type='text' class='smack-vtiger-settings' name='key' id='key' value="<?php echo sanitize_text_field($config['key']) ?>" onblur="save_zoho_settings('key', this.value);"/>
			</td> 
		</tr>
		<tr>
			<td  style='width:250px;padding-left:40px;'>
				<label id="innertext"> <?php echo esc_html__('Client Secret', WP_HELPDESK_INTEGRATION_SLUG); ?> </label><div style='float:right;'> : </div>
			</td>
			<td>
				<input type='text' class='smack-vtiger-settings' name='secret' id='secret' value="<?php echo sanitize_text_field($config['secret']) ?>" onblur="save_zoho_settings('secret', this.value);"/>
            </td> 
		</tr>
		<tr>
			<td  style='width:250px;padding-left:40px;'>
				<label id="innertext"> <?php echo esc_html__('Org ID', WP_HELPDESK_INTEGRATION_SLUG); ?> </label><div style='float:right;'> : </div>
			</td>
			<td style="display:flex;">
				<input type='text' class='smack-vtiger-settings' name='org_id' id='org_id' value="<?php echo sanitize_text_field($config['org_id']) ?>" onblur="save_zoho_settings('org_id', this.value);"/>
				<div style ="position:relative;top:5px;">
					<a class="tooltip"  href="#" style="padding-left:8px">
						<img src="<?php echo esc_url(WP_HELPDESK_INTEGRATION_DIR); ?>images/help.png">
							<span class="tooltipPostStatus">
							<img src="<?php echo esc_url(WP_HELPDESK_INTEGRATION_DIR); ?>images/callout.gif" class="callout">
							<ul>
								<li>Go to setup icon on the top right corner</li>
								<li>Click API under Developer Space</li>
								<li>Scroll down and copy your Orgid</li>
							</ul>
							</span> </a>
				</div>
			</td>
			<td>		
			</td>
		</tr>

		<tr>
			<td  style='width:250px;padding-left:40px;'>
				<label id="innertext"> <?php echo esc_html__('Callback Url', WP_HELPDESK_INTEGRATION_SLUG); ?> </label><div style='float:right;'> : </div>
			</td>

			<?php $callback_url = site_url().'/wp-admin/admin.php?page=wp-helpdesk-integration/index.php';?>

			<td>		
				<input type='text' class='smack-vtiger-settings' name='org_id' id='copyid' value="<?php echo esc_url($callback_url) ?>" /> <button type="button" onclick="copyFunction();">Copy text</button>
			</td> 
		</tr>

		<tr>
			<td  style='width:250px;padding-left:40px;'>
				<label id="innertext"> <?php echo esc_html__('Domain', WP_HELPDESK_INTEGRATION_SLUG); ?> </label><div style='float:right;'> : </div>
			</td>
			<td>
		
			<?php $config = get_option("smack_whi_zohodesk1_settings"); ?>

					<select name = "domainselect" id ="domainselect" onblur="save_zoho_settings('domain', this.value);"/>
						
					<optgroup label='Available Domains'>

						<option value='.com'<?php if($config['domain'] == '.com'): ?> selected=selected<?php endif; ?>> .com </option>	
						<option value='.in'<?php if($config['domain'] == '.in'): ?> selected=selected<?php endif; ?>> .in </option>
						<option value='.eu'<?php if($config['domain'] == '.eu'): ?> selected=selected<?php endif; ?>> .eu </option>		
							
					</optgroup>
						
					</select>
			
			</td> 
		</tr>
		
        </table>
	<table style="float:right;">
		<tr>
			<td>
				<p class="submit" style='position:absolute'>
				<input type="hidden" name="posted" value="<?php echo 'posted';?>">
				<input class="smack_settings_input_text" type="hidden" id="authkey" name="authkey" value="" />
				<input type="hidden" id="site_url" name="site_url" value="<?php echo esc_attr($siteurl) ;?>">
				<input type="hidden" id="active_plugin" name="active_plugin" value="<?php echo esc_attr($skinnyData['activatedplugin']); ?>">
				<input type="hidden" id="tickets_fields_tmp" name="tickets_fields_tmp" value="smack_whi_zohodesk_tickets_fields-tmp">
				<input type="hidden" id="contact_fields_tmp" name="contact_fields_tmp" value="smack_whi_zohodesk_contacts_fields-tmp">
		
				<div class="clearfix"></div>
				
				<?php if( !isset($config['refresh_token'])) {?>
					<div class="col-md-offset-10">
						<span style="padding-right:10px;">
						<a class="call-back-btn-authentication" ><input name="submit" type="button" value="<?php echo esc_attr__('Authenticate' , WP_HELPDESK_INTEGRATION_SLUG ); ?>" class="button-primary" onclick="redirectZoho();" /> </a>
						</span>
					</div>
				<?php } else { ?>
					<div class="col-md-offset-9">
						<span style="padding-right:10px;">
							<input type="button" id="save_crm_config" value="<?php echo esc_attr__('Save Helpdesk Configuration' , WP_HELPDESK_INTEGRATION_SLUG );?>" id="save"  class="button-primary"  onclick="saveHelpDeskConfiguration(this.id);" />
						</span>
					</div>
				<?php } ?>
		
				</span>
				</p>
			</td>
		</tr>
	</table>
	</div>
</form>

<div id="loading-sync" style="display: none; background:url(<?php echo esc_url(WP_HELPDESK_INTEGRATION_DIR);?>images/ajax-loaders.gif) no-repeat center #fff;"><?php echo esc_html__('Syncing', WP_HELPDESK_INTEGRATION_SLUG ); ?>...</div>
<div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_HELPDESK_INTEGRATION_DIR);?>images/ajax-loaders.gif) no-repeat center #fff;"><?php echo esc_html__('Please Wait' , WP_HELPDESK_INTEGRATION_SLUG ); ?>...</div>
