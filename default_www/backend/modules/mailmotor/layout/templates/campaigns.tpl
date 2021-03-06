{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblCampaigns|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_campaign'}" class="button icon iconFolderAdd" title="{$lblAddCampaign|ucfirst}">
			<span>{$lblAddCampaign|ucfirst}</span>
		</a>
	</div>
</div>

<form action="{$var|geturl:'mass_campaign_action'}" method="get" class="forkForms submitWithLink" id="campaigns">
	{option:datagrid}
		<div class="datagridHolder">
			{$datagrid}
		</div>
	{/option:datagrid}
	{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}
</form>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}