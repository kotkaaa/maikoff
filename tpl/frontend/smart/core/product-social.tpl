<div class="social-network">
    <a href="#" onclick="Share.facebook(window.location.href, document.title, '<{$smarty.const.WLCMS_HTTP_HOST|cat:$item.big_image}>', '<{$arCategory.meta_descr}>');">
        <img src="/images/smart/icons/fb.png" alt="">
    </a>
    <a href="#" onclick="Share.twitter(window.location.href, document.title);">
        <img src="/images/smart/icons/tw.png" alt="">
    </a>
    <{*<a href="#">
        <img src="/images/smart/icons/pi.png" alt="">
    </a>*}>
    <a href="#" onclick="Share.google(window.location.href);">
        <img src="/images/smart/icons/gp.png" alt="">
    </a>
    <{*<a href="#">
        <img src="/images/smart/icons/im.png" alt="">
    </a>*}>
</div>