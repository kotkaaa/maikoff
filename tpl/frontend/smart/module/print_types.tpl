<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
<div class="container clearfix">
    <div class="article-news">
        <{include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
        <h2><{$item.title}></h2>
        <{$item.fulldescr}>
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
        <br/>
<{/if}>
        <{include file="ajax/subscribe.tpl" position="right"}>
    </div>
</div>
<{else}>
<div class="top-banner">
    <div class="container">
        <h2>Чудеса технологий начинаются здесь</h2>
        <p>Благодаря профессиональному оборудованию,</p>
        <p>компания предлагает все существующие виды печати на изделиях</p>
    </div>
</div>
<div class="kind-print">
    <div class="above-block">
        <p>Наши специалисты подскажут, какой метод</p>
        <p>наиболее оптимальный для Вашего изображения.</p>
        <h2><{$arCategory.title}></h2>
    </div>
    <div class="container">
        <ul>
<{section name=i loop=$items}>
<{if $items[i].id==5}>
            <li>
                <img src="<{$items[i].image}>" alt="<{$items[i].title}>">
                <p class="kind-print-name">
                    <{$items[i].title}>
<{if $items[i].min_qty > 0}>
                    от <{$items[i].min_qty}> шт
<{/if}>
                </p>
                <p><{$items[i].descr|strip_tags}></p>
            </li>
<{else}>
            <li>
                <a href="<{if !empty($items[i].redirecturl)}><{$items[i].redirecturl}><{else}><{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}><{/if}>">
                    <img src="<{$items[i].image}>" alt="<{$items[i].title}>">
                </a>
                <p class="kind-print-name">
                    <a href="<{if !empty($items[i].redirecturl)}><{$items[i].redirecturl}><{else}><{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}><{/if}>">
                        <{$items[i].title}>
<{if $items[i].min_qty > 0}>
                        от <{$items[i].min_qty}> шт
<{/if}>
                    </a>
                </p>
                <p><{$items[i].descr|strip_tags}></p>
            </li>
<{/if}>
<{/section}>
        </ul>
    </div>
</div>
<{/if}>
<{include file='core/last-watched.tpl'}>
<{include file='core/contact-us.tpl'}>