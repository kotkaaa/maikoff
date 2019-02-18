<{* include file="ajax/_filter.tpl" fid=$filterID aid=$arKey $value='id' item=$arItem*}>
<{if $type==UrlFilters::TYPE_COLOR}>
<div class="color <{if $item.selected}>checked<{elseif $item.cnt==0}>disabled<{/if}>">
    <div class="pic">
        <a href="<{$item.url}>" style="background-color: #<{$item.hex}>" class="filter-element <{if $item.cnt==0}>disabled<{/if}>"></a>
    </div>
    <a href="<{$item.url}>" class="filter-element <{if $item.cnt==0}>disabled<{/if}>"><{$item[$title]}></a>
</div>
<{elseif $type==UrlFilters::TYPE_CATEGORY}>
<li class="<{if $item.selected}>checked<{elseif $item.cnt==0}>disabled<{/if}>" data-cnt="<{$item.cnt}>">
    <a class="filter-element <{if $item.cnt==0}>disabled<{/if}>" href="<{$item.url}>"><{$item[$title]}></a>
</li>
<{else}>
<li class="<{if $item.selected}>checked<{elseif $item.cnt==0}>disabled<{/if}>">
    <a href="<{$item.url}>" class="filter-element <{if $item.cnt==0}>disabled<{/if}>"><{$item[$title]}></a>
</li>
<{/if}>