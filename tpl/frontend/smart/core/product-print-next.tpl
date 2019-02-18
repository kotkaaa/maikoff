<div class="product-item product-next-page" id="product_<{$item.id}>_<{$item.product_id}>_<{$item.print_id}>">
    <div class="info">
        <a href="<{$arrPageData.pager->getUrl($arrPageData.pager->getNext())}>" class="img">
            <img src="<{$item.middle_image}>" class="default" alt="">
        </a>
        <p>
            <a href="<{$arrPageData.pager->getUrl($arrPageData.pager->getNext())}>" class="product-grid-name">Следующая страница</a>
        </p>
        <p class="product-grid-price"></p>
        <span class="next-page-icon"></span>
    </div>
</div>