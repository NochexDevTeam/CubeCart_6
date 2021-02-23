<?php

?>
<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
	<div id="Nochex_APC" class="tab_content">
  		<!--h3>{$TITLE}</h3-->
		<img src="https://www.nochex.com/logobase-secure-images/logobase-banners/clear-mp.png" alt="Nochex" style="height:100px;" />
  		<fieldset><legend>{$LANG.module.cubecart_settings}</legend>
			
			<div><label for="email">{$LANG.nochex_apc.mode_merchantid}</label><span><input name="module[email]" id="email" class="textbox" type="text" value="{$MODULE.email}" /></span></div>
			<div><label for="emailUSD">{$LANG.nochex_apc.mode_merchantidUSD}</label><span><input name="module[emailUSD]" id="emailUSD" class="textbox" type="text" value="{$MODULE.emailUSD}" /></span></div>
			<div><label for="emailEUR">{$LANG.nochex_apc.mode_merchantidEUR}</label><span><input name="module[emailEUR]" id="emailEUR" class="textbox" type="text" value="{$MODULE.emailEUR}" /></span></div>
  			<div><label for="mode_test">{$LANG.nochex_apc.mode_test}</label><span><input type="hidden" name="module[testMode]" id="testMode" class="toggle" value="{$MODULE.testMode}" /></span>
    		</div>
			<div><label for="hide">{$LANG.nochex_apc.mode_hide}</label><span><input type="hidden" name="module[hideMode]" id="hideMode" class="toggle" value="{$MODULE.hideMode}" /></span></div>						<div><label for="callback">{$LANG.nochex_apc.mode_callback}</label><span><input type="hidden" name="module[callback]" id="callback" class="toggle" value="{$MODULE.callback}" /></span></div>
			<div><label for="postage">{$LANG.nochex_apc.mode_postage}</label><span><input type="hidden" name="module[postageMode]" id="postageMode" class="toggle" value="{$MODULE.postageMode}" /></span>
			<div><label for="xml">{$LANG.nochex_apc.mode_xml}</label><span><input type="hidden" name="module[xmlMode]" id="xmlMode" class="toggle" value="{$MODULE.xmlMode}" /></span>
    		</div>
			
			<div><label for="default">{$LANG.nochex_apc.mode_default}</label><span><input type="hidden" name="module[default]" id="default" class="toggle" value="{$MODULE.default}" /></span></div>
			<div><label for="position">{$LANG.module.position}</label><span><input type="text" name="module[position]" id="position" class="textbox number" value="{$MODULE.position}" /></span></div>
			<div><label for="status">{$LANG.common.status}</label><span><input type="hidden" name="module[status]" id="status" class="toggle" value="{$MODULE.status}" /></span></div>
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
    		</div>
    	</fieldset>
    	
  		</div>
  		{$MODULE_ZONES}
  		<div class="form_control">
			<input type="submit" name="save" value="{$LANG.common.save}" />
  		</div>
  	<input type="hidden" name="token" value="{$SESSION_TOKEN}" />
</form>