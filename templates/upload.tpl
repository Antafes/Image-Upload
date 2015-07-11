{include file="header.tpl"}
<div id="upload">
	<form enctype="multipart/form-data" action="index.php?page=Upload" method="post">
		{add_form_salt formName='upload'}
		{$translator->gt('image')}: <input type="file" name="fileupload[]" /><br />
		<input type="submit" value="{$translator->gt('upload')}" />
	</form>
	{if $resultMessage}
		<div style="margin-top: 20px;">
			{$resultMessage}
		</div>
	{/if}
</div>
{include file="footer.tpl"}