<{if !empty($article)}>
<div class="product-article clearfix">
    <h4><a href="<{include file="core/href_item.tpl" arCategory=$article.arCategory arItem=$article params=""}>"><{$article.title}></a></h4>
    <a href="<{include file="core/href_item.tpl" arCategory=$article.arCategory arItem=$article params=""}>">
        <img src="<{$article.image}>" alt="<{$article.title}>"/>
    </a>
    <{$article.descr|strip_tags}><br>
    <a href="<{include file="core/href_item.tpl" arCategory=$article.arCategory arItem=$article params=""}>" class="read-more">Подробнее</a>
</div>
<{/if}>