{include file="header.tpl"}
<div id="index">
	<div class="row">
	{foreach $images->getAsArray() as $image}
		<div class="left">
			<a href="index.php?image={$image.hash}{$image.addDateTime->format('Y-m-d')}">
				<img src="index.php?page=Thumb&amp;image={$image.hash}{$image.addDateTime->format('Y-m-d')}" />
			</a><br />
			<a href="index.php?page=Index&amp;delete={$image.imageId}">{$translator->gt('delete')}</a>
		</div>
		{if $image@iteration is div by 5}
			<br style="clear: both;" />
			</div>
			<div class="row">
		{/if}
	{/foreach}
	</div>
</div>
{include file="footer.tpl"}