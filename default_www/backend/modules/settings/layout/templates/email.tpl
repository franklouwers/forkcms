{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblGeneralSettings|ucfirst}</h2>
</div>

{form:settingsEmail}
	<div class="box">
		<div class="heading">
			<h3>{$lblEmail|ucfirst}</h3>
		</div>
		<div class="options">
			<h4>{$lblSendingEmails|ucfirst}</h4>
			<p>{$msgHelpSendingEmails}</p>
			<p>
				<label for="mailerType">{$ddmMailerType} {$ddmMailerTypeError}</label>
			</p>
		</div>
		<div class="options">
			<h4>{$lblFrom|ucfirst}</h4>
			<p>{$msgHelpEmailFrom}</p>
			<p>
				<label for="mailerFromName">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerFromName} {$txtMailerFromNameError}
			</p>
			<p>
				<label for="mailerFromEmail">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerFromEmail} {$txtMailerFromEmailError}
			</p>
		</div>
		<div class="options">
			<h4>{$lblTo|ucfirst}</h4>
			<p>{$msgHelpEmailTo}</p>
			<p>
				<label for="mailerToName">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerToName} {$txtMailerToNameError}
			</p>
			<p>
				<label for="mailerToEmail">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerToEmail} {$txtMailerToEmailError}
			</p>
		</div>
		<div class="options">
			<h4>{$lblReplyTo|ucfirst}</h4>
			<p>
				<label for="mailerReplyToName">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerReplyToName} {$txtMailerReplyToNameError}
			</p>
			<p>
				<label for="mailerReplyToEmail">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerReplyToEmail} {$txtMailerReplyToEmailError}
			</p>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblSMTP|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="smtpServer">{$lblServer|ucfirst} &amp; {$lblPort}</label>
				{$txtSmtpServer}:{$txtSmtpPort} {$txtSmtpServerError} {$txtSmtpPortError}
				<span class="helpTxt">{$msgHelpSMTPServer}</span>
			</p>
			<p>
				<label for="smtpUsername">{$lblUsername|ucfirst}</label>
				{$txtSmtpUsername} {$txtSmtpUsernameError}
			</p>
			<p>
				<label for="smtpPassword">{$lblPassword|ucfirst}</label>
				{$txtSmtpPassword} {$txtSmtpPasswordError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settingsEmail}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}