{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblBlog|ucfirst}: {$lblComments}</h2>
</div>

<div id="tabs" class="tabs">
	<ul>
		<li><a href="#tabPublished">{$lblPublished|ucfirst} ({$numPublished})</a></li>
		<li><a href="#tabModeration">{$lblWaitingForModeration|ucfirst} ({$numModeration})</a></li>
		<li><a href="#tabSpam">{$lblSpam|ucfirst} ({$numSpam})</a></li>
	</ul>

	<div id="tabPublished">
		{option:dgPublished}
			<form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms" id="commentsPublished">
				<div class="datagridHolder">
					<input type="hidden" name="from" value="published" />
					{$dgPublished}
				</div>
			</form>
		{/option:dgPublished}
		{option:!dgPublished}{$msgNoComments}{/option:!dgPublished}
	</div>

	<div id="tabModeration">
		{option:dgModeration}
			<form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms" id="commentsModeration">
				<div class="datagridHolder">
					<input type="hidden" name="from" value="moderation" />
					{$dgModeration}
				</div>
			</form>
		{/option:dgModeration}
		{option:!dgModeration}{$msgNoComments}{/option:!dgModeration}
	</div>

	<div id="tabSpam">
		{option:dgSpam}
			<form action="{$var|geturl:'mass_comment_action'}" method="get" class="forkForms" id="commentsSpam">
				<div class="datagridHolder">
					<input type="hidden" name="from" value="spam" />
					{$dgSpam}
				</div>
			</form>
		{/option:dgSpam}
		{option:!dgSpam}{$msgNoComments}{/option:!dgSpam}
	</div>
</div>

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
	<p>{$msgConfirmMassDelete}</p>
</div>
<div id="confirmSpam" title="{$lblSpam|ucfirst}?" style="display: none;">
	<p>{$msgConfirmMassSpam}</p>
</div>


{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}