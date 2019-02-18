<{* REQUIRE VARS: arrPager=Pager, page=int, showTitle=[0|1], showFirstLast=[0|1], showPrevNext=[0|1], showAll=[0|1] *}>
<{* arrPager have keys(all, first, last, prev, next, count, pages) and methods getUrl($page) and other for get key *}>
<{if !$IS_AJAX}>
<div class="pagination <{if $arrPager->getCount() < 2}>hidden<{/if}>" id="pager">
<{/if}>
    <div class="container">
<{if $showFirstLast and $page < $arrPager->getCount()}>
        <button class="btn btn-xxl lg btn-load-more" data-href="<{$arrPager->getUrl($arrPager->getNext())}>">Загрузить еще</button>
<{/if}>
        <ul>
<{foreach name=i from=$arrPager->getPages() item=iItem}>
            <li class="<{if $iItem == $page}>selected<{/if}> btn">
<{if $arrPager->getSeparator() == $iItem}>
                <a href="#">...</a>
<{elseif $iItem == $page}>
                <a href="#"><{$iItem}></a>
<{else}>
                <a href="<{$arrPager->getUrl($iItem)}>"><{$iItem}></a>
<{/if}>
            </li>
<{/foreach}>
        </ul>
    </div>
<{if !$IS_AJAX}>
</div>
<{/if}>