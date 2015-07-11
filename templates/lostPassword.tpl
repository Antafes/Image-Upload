{include file="header.tpl"}
<div id="login">
	{if $error}
		<div class="error">{$translator->gt($error)}</div>
	{/if}
	{if $message}
		<div class="message">{$translator->gt($message)}</div>
	{/if}
	<form method="post" action="index.php?page=LostPassword">
		<table>
			<tr>
				<td>{$translator->gt('email')}:</td>
				<td>
					<input type="text" name="email" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{add_form_salt formName='lostPassword'}
					<input type="submit" value="{$translator->gt('retrievePassword')}" />
				</td>
			</tr>
		</table>
	</form>
</div>
{include file="footer.tpl"}