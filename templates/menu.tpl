<div id="menu">
	{if $smarty.session.userId}
		<a class="button{if !$smarty.get.page || $smarty.get.page == 'Index'} active{/if}" href="index.php?page=Index">{$translator->gt('index')}</a>
		<a class="button{if $smarty.get.page == 'Upload'} active{/if}" href="index.php?page=Upload">{$translator->gt('upload')}</a>
		{if $isAdmin}
			<a class="button{if $smarty.get.page == 'Admin'} active{/if}" href="index.php?page=Admin">{$translator->gt('admin')}</a>
		{/if}
		<a class="button{if $smarty.get.page == 'Logout'} active{/if}" href="index.php?page=Logout">{$translator->gt('logout')}</a>
	{else}
		<a class="button{if $smarty.get.page == 'Login'} active{/if}" href="index.php?page=Login">{$translator->gt('login')}</a>
		<a class="button{if $smarty.get.page == 'Register'} active{/if}" href="index.php?page=Register">{$translator->gt('register')}</a>
	{/if}
	<div class="clear"></div>
</div>