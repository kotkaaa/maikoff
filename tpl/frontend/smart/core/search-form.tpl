<div class="search">
    <button type="submit" class="btn-search"></button>
    <div class="form hidden">
        <form action="<{include file='core/href.tpl' arCategory=$arrModules.search}>" method="GET" id="qSearchForm">
            <input type="search" name="stext" id="qSearchText" placeholder="найти название футболки, тематика принта" value="<{if !empty($arrPageData.stext)}><{$arrPageData.stext}><{/if}>">
            <button class="btn-find" type="submit"></button>
            <button class="btn-close" type="reset">&times;</button>
        </form>
    </div>
</div>