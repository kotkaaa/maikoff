<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
<div class="product-container" data-item-id="<{$item.id}>">
    <div class="container clearfix">
        <{include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
        <h1 class="heading-title"><{$arrPageData.headTitle}></h1>
<{if !empty($item.pcode)}>
        <p class="product-sku">артикул <{$item.pcode}></p>
<{/if}>
        <div class="product-card product-print-card" data-item-id="<{$item.id}>" data-idkey="<{$item.idKey}>">
            <div class="left-col">
                <div class="product-image">
                    <{include file='core/product-print-gallery.tpl' showThumbs=0}>
                </div>
<{if !empty($item.size_grid)}>
                <div class="size-table-wrap">
                    <{include file="core/size-table.tpl"}>
                </div>
<{/if}>
                <div class="print-method">
                    <div class="print-wrapper">
                        <h4>Методы печати</h4>
                        <ul>
                            <li>
                                <a href="<{include file="core/href.tpl" arCategory=$arrModules.landing_termoperenos}>" target="_blank">Термоперенос</a> немецкими пленками PoliTape -
                                срок експлуатации 50 стирок
                            </li>
                            <li>
                                <a href="<{include file="core/href.tpl" arCategory=$arrModules.landing_pramaya_pechat}>">Прямая цифровая печать</a> по ткани -
                                срок эксплуатации 30 стирок
                            </li>
                        </ul>
                        <p>Нанесение не трескается, не отклеивается и сохраняет товарный вид при правильной эксплуатации.</p>
                    </div>
                </div>
                <div class="care-recomendation">
                    <div class="care-wrapper">
                        <h4>Рекомедации по уходу</h4>
                        <ul>
                            <li>
                                <div class="icon"></div>
                                <p>Не допускать контакта поверхности утюга с изображением</p>
                            </li>
                            <li>
                                <div class="icon"></div>
                                <p>Стирка при температупе 40° в бережном режиме, не использовать отбеливатель</p>
                            </li>
                            <li>
                                <div class="icon"></div>
                                <p>При правильном уходе изделия выдерживают 50 и более стирок</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="right-col">
                <{include file="core/product-print-details.tpl"}>
            </div>
        </div>
    </div>
</div>
<{include file='core/last-watched.tpl'}>
<h2 class="feature-sign">Заказать печать просто</h2>
<{include file='core/features-2.tpl'}>
<{include file='core/contact-us.tpl'}>
<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>
<{else}>
<div class="content-box clearfix">
    <{include file="core/breadcrumb.tpl" arrBreadCrumb=$arrPageData.arrBreadCrumb}>
    <div class="flex">
        <div class="right-box">
            <{include file="core/content-top.tpl"}>
            <div class="product-grid" id="products">
                <{include file='ajax/products.tpl' items=$items}>
            </div>
            <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=1 showPrevNext=0 showAll=0}>
            <{include file="core/content-bottom.tpl"}>
        </div>
        <div class="left-box">
            <{include file='ajax/filter.tpl'}>
        </div>
    </div>
</div>
<{/if}>
<{include file='core/seo-text.tpl'}>