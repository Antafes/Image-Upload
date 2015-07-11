{include file="header.tpl"}
<div id="admin">
	<table class="collapse">
		<thead>
			<tr>
				<th>{$translator->gt('userId')}</th>
				<th>{$translator->gt('username')}</th>
				<th>{$translator->gt('status')}</th>
				<th>{$translator->gt('admin')}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$userList item='user'}
				<tr class="{cycle values='odd,even'}">
					<td>{$user->getUserId()}</td>
					<td>{$user->getName()}</td>
					<td class="centered">
						{if $user->getStatus()}
							{$translator->gt('active')}
						{else}
							<a href="index.php?page=Admin&amp;activate={$user->getUserId()}">
								{$translator->gt('activate')}
							</a>
						{/if}
					</td>
					<td class="centered">
						{if $user->getAdmin() && $user->getUserId() != $smarty.session.userId}
							<a href="index.php?page=Admin&amp;removeAdmin={$user->getUserId()}">
								{$translator->gt('removeAdmin')}
							</a>
						{elseif !$user->getAdmin() && $user->getStatus()}
							<a href="index.php?page=Admin&amp;setAdmin={$user->getUserId()}">
								{$translator->gt('setAdmin')}
							</a>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div style="margin-top: 10px;">
		<a class="button" href="index.php?page=Admin&amp;reCreateThumbnails=1">{$translator->gt('reCreateThumbnails')}</a>
	</div>
</div>
{include file="footer.tpl"}