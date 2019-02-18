<{assign var=cnt value=0}>
<{foreach from=$item.sizes item=size}>
<{$cnt = ($cnt + $Basket->qty($size.idKey))}>
<{/foreach}>
<button class="btn btn-default btn-l add-to-cart <{if $cnt}>in-cart<{/if}>" data-cnt="<{$cnt}>">
    <span class="buy-text"></span>
    <span class="cnt <{if !$cnt}>hidden<{/if}>"><{$cnt}></span>
</button>