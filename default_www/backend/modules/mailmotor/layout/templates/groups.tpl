{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_group'}" class="button icon iconAdd" title="{$lblAddGroup|ucfirst}">
			<span>{$lblAddGroup|ucfirst}</span>
		</a>
	</div>
</div>

{option:datagrid}
<form action="{$var|geturl:'mass_group_action'}" method="get" class="forkForms submitWithLink" id="groups">
	<div class="datagridHolder">
		{$datagrid}
	</div>
</form>
{/option:datagrid}
{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}