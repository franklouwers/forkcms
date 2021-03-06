{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblBounces|ucfirst} {$lblFor} &ldquo;{$mailing['name']}&rdquo;</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'delete_bounces'}&amp;mailing_id={$mailing['id']}" class="button icon iconDelete" title="{$msgDeleteBounces|ucfirst}">
			<span>{$msgDeleteBounces|ucfirst}</span>
		</a>
	</div>
</div>

{option:datagrid}
<form action="{$var|geturl:'mass_bounces_action'}" method="get" class="forkForms submitWithLink" id="bounces">
	<div class="datagridHolder">
		{$datagrid}
	</div>
</form>
{/option:datagrid}

<div class="buttonHolderLeft">
	<a href="{$var|geturl:'statistics'}&amp;id={$mailing['id']}" class="button" title="{$lblStatistics|ucfirst}">
		<span>{$msgBackToMailings|sprintf:{$mailing['name']}}</span>
	</a>
</div>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}