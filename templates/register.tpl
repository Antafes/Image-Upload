{include file="header.tpl"}
<div id="register">
	{if $error}
		<div class="error">{$error}</div>
	{/if}
	{if $message}
		<div class="message">{$message}</div>
	{/if}
	<form method="post" action="index.php?page=Register">
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
				<td>{$translator->gt('repeatPassword')}:</td>
				<td>
					<input type="password" name="repeatPassword" />
				</td>
			</tr>
			<tr>
				<td>{$translator->gt('email')}:</td>
				<td>
					<input type="text" name="email" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{add_form_salt formName="register"}
					<input type="submit" value="{$translator->gt('register')}" />
				</td>
			</tr>
		</table>
	</form>
</div>
{include file="footer.tpl"}