{assign var=week value=0}
{assign var=year value=0}
{assign var=loopnum value=0}
{assign var=s_count value=$storms|@count}
{assign var=item_per_col value=$s_count/2}
<h3>{t 1=$username}Storms de %1{/t}</h3>
<div itemscope itemtype="http://schema.org/Person">
	<div class="left _40">
		<img src="{$avatar}" alt="{$username|escape}" itemprop="image" style="float:left; margin: 0 5px 0 0;" />
		<ul class="nolist nomargin">
			<li><span itemprop="name"><b>{$prenom|ucfirst} {$nom|ucfirst}</b></span></li>
			<li>{t 1=$member_since|date_format:"%d %B %Y"}Membre de Public-Storm depuis le %1{/t}</li>
			<li>{t}Langue :{/t} {t}{$lang}{/t}</li>
		</ul>
	</div>
	<div class="left">
		<a href="rss/">
			<img width="14" height="14" align="top" src="{$theme_dir}/img/rss.png" alt="{t}Flux Rss{/t} {$username|escape}" title="{t}Flux Rss{/t} {$username|escape}" />
			{t 1=$username|ucwords}Flux Rss de l'auteur %1{/t}
		</a>
	</div>
</div>
<div style="clear:both;">&nbsp;</div>

{include file="./pagination.tpl" tabbed=false login=$login nb_pages=$nb_pages current_page=$current_page base_url_http=$base_url_http}

<div class="table _100">
	<div class="table-row">
		<div class="table-cell _50">
			<ul class="liste">
				{foreach from=$storms item=storm}
				{if $storm.root ne ""}
					{if $year ne $storm.date|date_format:"%Y"}
						<li class="year">{$storm.date|date_format:"%Y"}</li>
					{/if}
					
					{if $loopnum ge $item_per_col|ceil}
						</ul></div>
						{assign var=loopnum value=0}
						<div class="table-cell _50">
							<ul class="liste">
								{if $week eq $storm.date|date_format:"%W"}
									<li class="cap">{t}Semaine{/t} {$storm.date|date_format:"%W"} {t}(suite){/t}</li>
								{/if}
					{/if}
					{if $week ne $storm.date|date_format:"%W"}
						<li class="cap">{t}Semaine{/t} {$storm.date|date_format:"%W"}</li>
					{/if}
					{if $loopnum ge $item_per_col|ceil}
						</ul></div>
						{assign var=loopnum value=0}
						<div class="table-cell _50"><ul class="liste">
					{/if}
					<li>
						{if $storm.hearts}<span class="sprite heart1" title="{t}I love this Storm !{/t}"></span>{/if} <a href="{$base_url}/storm/{$storm.permaname}/" class="storm">{$storm.root|ucfirst}</a>
						<a href="/backend/storm/{$storm.permaname}/rss.php"><img width="14" height="14" align="top" src="{$theme_dir}/img/rss.png" alt="{t 1=$storm.permaname}Flux Rss des suggestions de '%1'{/t}" title="{t 1=$storm.permaname}Flux Rss des suggestions de '%1'{/t}" /></a>, {$storm.date|date:"d/m/Y"}
					</li>
					{assign var=week value=$storm.date|date_format:"%W"}
					{assign var=year value=$storm.date|date_format:"%Y"}
				{/if}
				{assign var=loopnum value=$loopnum+1}
				{/foreach}

			</ul>
		</div>
	</div>
</div>

{include file="./pagination.tpl" tabbed=false login=$login nb_pages=$nb_pages current_page=$current_page base_url_http=$base_url_http}