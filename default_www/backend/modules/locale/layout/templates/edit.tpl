{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:edit}
	<div class="box">
		<div class="heading">
			<h3>{$lblTranslations|ucfirst}: {$msgEditTranslation|sprintf:{$name}}</h3>
		</div>
		<div class="options">
			<div class="horizontal">
				<p>
					<label for="name">{$lblReferenceCode|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtName} {$txtNameError}
					<span class="helpTxt">{$msgHelpEditName}</span>
				</p>

				<p>
					<label for="value">{$lblTranslation|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtValue} {$txtValueError}
					<span class="helpTxt">{$msgHelpEditValue}</span>
				</p>

				<p>
					<label for="language">{$lblLanguage|ucfirst}</label>
					{$ddmLanguage} {$ddmLanguageError}
				</p>

				<p>
					<label for="application">{$lblApplication|ucfirst}</label>
					{$ddmApplication} {$ddmApplicationError}
				</p>

				<p>
					<label for="module">{$lblModule|ucfirst}</label>
					{$ddmModule} {$ddmModuleError}
				</p>

				<p>
					<label for="type">{$lblType|ucfirst}</label>
					{$ddmType} {$ddmTypeError}
				</p>

			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
			</div>
		</div>
	</div>
{/form:edit}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}