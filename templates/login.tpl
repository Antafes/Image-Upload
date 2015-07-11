{include file="header.tpl"}
<div id="login">
	{if $error}
		<div class="error">{$error}</div>
	{/if}
	<form method="post" action="index.php?page=Login">
		<table>
			<tr>
				<td>{$translator->gt('username')}:</td>
				<td>
					<input type="text" name="username" />
				</td>
			</tr>
			<tr>
				<td>{$translator->gt('password')}:</td>
				<td>
					<input type="password" name="password" />
				</td>
			</tr>
			<tr>
				<td>
					{add_form_salt formName='login'}
					<input type="submit" value="{$translator->gt('login')}" />
				</td>
				<td>
					<a href="index.php?page=LostPassword">{$translator->gt('lostPassword')}</a>
				</td>
			</tr>
		</table>
	</form>
</div>
{include file="footer.tpl"}