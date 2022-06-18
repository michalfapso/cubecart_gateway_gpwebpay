<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@devellion.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 */
?>
<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
	<div id="GPWebpay" class="tab_content">
  		<h3>{$TITLE}</h3>
		<p class="copyText">{$LANG.gpwebpay.module_description}</p>

  		<fieldset><legend>{$LANG.module.cubecart_settings}</legend>
			<div><label for="status">{$LANG.common.status}</label><span><input type="hidden" name="module[status]" id="status" class="toggle" value="{$MODULE.status}" /></span></div>
			<div><label for="position">{$LANG.module.position}</label><span><input type="text" name="module[position]" id="position" class="textbox number" value="{$MODULE.position}" /></span></div>
			<div>
				<label for="scope">{$LANG.module.scope}</label>
				<span>
					<select name="module[scope]">
						<option value="both" {$SELECT_scope_both}>{$LANG.module.both}</option>
						<option value="main" {$SELECT_scope_main}>{$LANG.module.main}</option>
						<option value="mobile" {$SELECT_scope_mobile}>{$LANG.module.mobile}</option>
					</select>
				</span>
			</div>
			<div><label for="default">{$LANG.common.default}</label><span><input type="hidden" name="module[default]" id="default" class="toggle" value="{$MODULE.default}" /></span></div>
			<div><label for="description">{$LANG.common.description} *</label><span><input name="module[desc]" id="description" class="textbox" type="text" value="{$MODULE.desc}" /></span></div>
			<div><label for="merchantNumber">{$LANG.gpwebpay.merchant_number}</label><span><input name="module[merchantNumber]" id="merchantNumber" class="textbox" type="text" value="{$MODULE.merchantNumber}" /></span></div>

			<div>
				<label for="environment">{$LANG.gpwebpay.environment}</label>
				<span>
					<select name="module[environment]" id="environment">
						<option value="testing"    {$SELECT_environment_testing}   >{$LANG.gpwebpay.environment_testing}</option>
						<option value="production" {$SELECT_environment_production}>{$LANG.gpwebpay.environment_production}</option>
					</select>
				</span>
			</div>

  		</fieldset>
		  
  		<fieldset><legend>{$LANG.gpwebpay.keys_info}</legend>
			<div><label for="privateKeyPassword">  {$LANG.gpwebpay.private_key_password  }</label><span><input name="module[privateKeyPassword]"  id="privateKeyPassword"  class="textbox" type="password" value="{$MODULE.privateKeyPassword}"  /></span></div>
			<div><label for="privateKeyFilename">  {$LANG.gpwebpay.private_key_filename  }</label><span><input name="module[privateKeyFilename]"  id="privateKeyFilename"  class="textbox" type="text"     value="{$MODULE.privateKeyFilename}"  /></span></div>
			<div><label for="publicKeyFilename">   {$LANG.gpwebpay.public_key_filename   }</label><span><input name="module[publicKeyFilename]"   id="publicKeyFilename"   class="textbox" type="text"     value="{$MODULE.publicKeyFilename}"   /></span></div>
			<div><label for="publicGpKeyFilename"> {$LANG.gpwebpay.public_gp_key_filename}</label><span><input name="module[publicGpKeyFilename]" id="publicGpKeyFilename" class="textbox" type="text"     value="{$MODULE.publicGpKeyFilename}" /></span></div>
		</fieldset>

<!--
  		<fieldset><legend>{$LANG.gpwebpay.gpwebpay_settings}</legend>
  			<p>{$LANG.module.3rd_party_settings_desc}</p>
  			<div><label for="direct_return">{$LANG.gpwebpay.direct_return}</label><span><input name="direct_return" id="direct_return" class="textbox" type="text" value="{$LANG.gpwebpay.direct_return_value}" readonly="readonly" /></span></div>
  			<div><label for="approved_url">{$LANG.gpwebpay.approved_url}</label><span><input name="approved_url" id="approved_url" class="textbox" type="text" value="{$STORE_URL}/index.php?_g=rm&type=gateway&cmd=process&module=GPWebpay" readonly="readonly" /></span></div>
  		</fieldset>
-->
  		<p>{$LANG.module.description_options}</p>
  		</div>
  		{$MODULE_ZONES}
  		<div class="form_control">
			<input type="submit" name="save" value="{$LANG.common.save}" />
  		</div>
  	<input type="hidden" name="token" value="{$SESSION_TOKEN}" />
</form>