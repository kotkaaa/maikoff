<div class="dropdown-list" id="control_view">
<{if !empty($arrPageData.arSorting)}>
    <select class="select" onchange="window.location.assign($(this).find('option:selected').data('value'));">
<{foreach name=i from=$arrPageData.arSorting key=sortID item=sorting}>
        <option value="<{$sorting.title}>" data-value="<{$sorting.url}>"<{if $sorting.active}> selected<{/if}>><{$sorting.title}></option>
<{/foreach}>
    </select>
<{/if}>
</div>