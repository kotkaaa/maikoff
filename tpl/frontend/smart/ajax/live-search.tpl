<{if !empty($items)}>
<div class="live-search">
    <div class="results">
        <{section name=i loop=$items}>
            <div class="result">
                <div class="img">
                    <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                        <img src="<{$items[i].small_image}>" alt=""/>
                    </a>
                </div>
                <div class="info">
                    <div class="title">
                        <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>"><{$items[i].title}></a>
                    </div>
                    <{$items[i].color_title}><br>
                    Артикул <{$items[i].pcode}>
                </div>
                <div class="price">
                    <{$items[i].price|number_format:0:".":" "}>
                </div>
            </div>
        <{/section}>
    </div>
    <div class="total">
        <a href="<{include file='core/href.tpl' arCategory=$arrModules.search params=""}>?stext=<{$arrPageData.stext}>" class="btn btn-primary btn-xl">Перейти на все результаты поиска</a>
    </div>
</div>
<{/if}>