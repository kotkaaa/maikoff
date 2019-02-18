<div class="container clearfix">
    <div class="article-news">
        <{include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
        <h1><{$item.title}></h1>
        <{$item.fulldescr}>
<{else}>
        <h1><{$arCategory.title}></h1>
<{section name=i loop=$items}>
        <div class="article clearfix">
            <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                <img src="<{$items[i].image}>" alt="<{$items[i].title}>">
            </a>
            <div class="article-info">
                <p>
                    <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>"><{$items[i].title}></a>
                </p>
                <p class="article-date"><{$HTMLHelper->RuDateFormat($items[i].created, "%#d %bg %Y")|ucfirst}></p>
                <p><{$items[i].descr|strip_tags}></p>
                <br>
                <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>" class="read-more">Читать далее</a>
            </div>
        </div>
<{/section}>
<{if $arrPageData.total_pages>1}>
        <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
        <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=1}>
        <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/if}>
    </div>
    <div class="popular-articles">
<{if !empty($arrPageData.arPopular)}>
        <h2>Популярные статьи</h2>
        <ul>
<{foreach from=$arrPageData.arPopular item=arPopular}>
            <li>
                <a href="<{include file="core/href_item.tpl" arCategory=$arrModules.news arItem=$arPopular params=""}>"><{$arPopular.title}></a>
            </li>
<{/foreach}>
        </ul>
        <br>
<{/if}>
        <{include file="ajax/subscribe.tpl" position="right"}>
    </div>
</div>
<{include file='core/last-watched.tpl'}>