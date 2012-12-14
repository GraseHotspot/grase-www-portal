<div class='paginate'>

 

{if $currentpage > 0}
 <a href="?allsessions&page=0">{t}First{/t}</a>
 <a href="?allsessions&page={$currentpage-1}">{t}Prev{/t}</a> 
{/if}
{*{t 1=$currentpage 2=$pages}Displaying page %1 of %2{/t} *}
{if $currentpage < $pages}
 <a href="?allsessions&page={$currentpage+1}">{t}Next{/t}</a> 
 <a href="?allsessions&page={$pages}">{t}Last{/t}</a>
{/if}
<br/>

{section name=paginate start=0 loop=$pages+1 step=1}
{if $smarty.section.paginate.index != 0} | {/if}
{if $smarty.section.paginate.index == $currentpage}<strong>{$smarty.section.paginate.index}</strong>{else}
<a style="display:inline-block" href="?allsessions&page={$smarty.section.paginate.index}">{$smarty.section.paginate.index}</a>
{/if}
{/section}




</div>
