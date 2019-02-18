<button class="btn btn-default btn-l add-to-cart <{if $Basket->isSetKey($item.idKey)}>in-cart<{/if}>" data-idkey="<{$item.idKey}>">
    <span class="buy-text"></span>
</button>