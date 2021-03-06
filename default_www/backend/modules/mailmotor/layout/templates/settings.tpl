{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblMailmotor|ucfirst}</h2>
</div>

{option:!clientId}
	<div class="generalMessage infoMessage content">
		<p><strong>{$msgConfigurationError}</strong></p>
		<ul class="pb0">
			{option:!account}<li>{$errNoCMAccount}</li>{/option:!account}
			{option:account}<li>{$errNoCMClientID}</li>{/option:account}
		</ul>
	</div>
{/option:!clientId}

{form:settings}
	<div class="box horizontal{option:account} hidden{/option:account}" id="accountBox">
		<div class="heading">
			<h3>CampaignMonitor - Account</h3>
		</div>

		<div class="options">
			<p>
				<label for="url">{$lblURL|uppercase} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
				{$txtUrl} {$txtUrlError}
				<span class="helpTxt">{$msgHelpCMURL}</span>
			</p>
			<p>
				<label for="username">{$lblUsername|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
				{$txtUsername} {$txtUsernameError}
			</p>
			<p>
				<label for="password">{$lblPassword|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
				{$txtPassword} {$txtPasswordError}
			</p>
			<div class="buttonHolder">
				<a id="linkAccount" href="#" class="button"><span>{$msgLinkCMAccount}</span></a>
			</div>
		</div>
	</div>

	<div id="clientIDBox"{option:!account} class="hidden"{/option:!account}>
		<div class="box horizontal">
			<div class="heading">
				<h3>CampaignMonitor - {$lblClientID|ucfirst} </h3>
			</div>

			{option:clientId}
			<div class="options id">
				<label for="clientId">{$lblClientID|ucfirst}</label>
				{$clientId}
			</div>
			{/option:clientId}

			{option:!clientId}
			<div class="options generate">
				<p class="formError"><strong>{$msgNoClientID}</strong></p>
				<p>
					<label for="companyName">{$lblCompanyName|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtCompanyName} {$txtCompanyNameError}
				</p>
				<p>
					<label for="contactName">{$lblContactName|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtContactName} {$txtContactNameError}
				</p>
				<p>
					<label for="contactEmail">{$lblEmailAddress|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtContactEmail} {$txtContactEmailError}
				</p>
				<p>
					<label for="countries">{$lblCountry|ucfirst}</label>
					{$ddmCountries} {$ddmCountriesError}
				</p>
				<p>
					<label for="timezones">{$lblTimezone|ucfirst}</label>
					{$ddmTimezones} {$ddmTimezonesError}
				</p>
			</div>
			{/option:!clientId}
		</div>

		{option:clientId}
		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblSender|ucfirst}</h3>
			</div>

			<div class="options">
				<p>
					<label for="fromName">{$lblName|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtFromName} {$txtFromNameError}
				</p>

				<p>
					<label for="fromEmail">{$lblEmailAddress|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtFromEmail} {$txtFromEmailError}
				</p>
			</div>
		</div>

		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblReplyTo|ucfirst}</h3>
			</div>

			<div class="options">
				<p>
					<label for="replyToEmail">{$lblEmailAddress|ucfirst} <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtReplyToEmail} {$txtReplyToEmailError}
				</p>
			</div>
		</div>

		<div class="box">
			<div class="heading">
				<h3>{$lblPlainTextVersion|ucfirst}</h3>
			</div>

			<div class="options">
				<ul class="inputList p0">
					<li>{$chkPlainTextEditable} <label for="plainTextEditable">{$msgPlainTextEditable|ucfirst}</label></li>
				</ul>
			</div>
		</div>

		{option:userIsGod}
		<div class="box horizontal">
			<div class="heading">
				<h3>{$lblPricePerEmail|ucfirst}</h3>
			</div>

			<div class="options">
				<p>
					<label for="pricePerEmail">{$lblPrice|ucfirst} in &euro; <abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtPricePerEmail} {$txtPricePerEmailError}
				</p>
			</div>
		</div>
		{/option:userIsGod}
		{/option:clientId}

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
			</div>
		</div>
	</div>
{/form:settings}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}