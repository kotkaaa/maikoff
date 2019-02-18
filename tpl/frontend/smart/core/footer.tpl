<footer>
    <div class="container">
        <div class="section">
            <{include file='core/footer-links.tpl'}>
            <{include file="menu/bottom.tpl" arItems=$bottomMenu marginLevel=0}>
            <{assign var=catalogBottomMenu value=array()}>
<{if !empty($catalogMenu)}>
            <div class="footer-col menu"> 
                <div class="heading-col"> <span>Каталог принтов</span> </div> 
                <div class="flex"> 
                    <ul class="list-links">
<{section name=i loop=$catalogMenu}>
                        <li>
<{if !$catalogMenu[i].separator and $catalogMenu[i].id!=$arCategory.id}>
                            <a href="<{include file="core/href.tpl" arCategory=$catalogMenu[i]}>">
<{/if}>
                                <{$catalogMenu[i].title}>
<{if !$catalogMenu[i].separator and $catalogMenu[i].id!=$arCategory.id}>
                            </a>
<{/if}>
                        </li>
<{if $smarty.section.i.index%15==0 and !$smarty.section.i.first and !$smarty.section.i.last}>
                    </ul>
                    <ul class="list-links">
<{/if}>
<{/section}>
                    </ul>
                </div>
            </div>
<{/if}>
        </div>
        <div class="hr"></div>
        <{include file='core/copyrights.tpl'}>
        <div class="bottom-section">
            <div class="content bottom-text">
                <p class="text">Заказать футболку, или печать на футболках можно с доставкой в Ваш город: 
                    Киев, Днепр, Винница, Житомир, Запорожье, Ивано-Франковск, Кропивницкий, 
                    Каменское, Луцк, Львов, Николаев, Одесса, Ровно, Сумы, Ужгород, Харьков, 
                    Херсон, Хмельницкий, Черкассы, Черновцы, Чернигов, и по другим областям в Украине.
                </p>
            </div>
        </div>
    </div>
</footer>
<div id="scrollup">
    <img onclick="$('html, body').animate({scrollTop: 0}, 400);" src="/images/site/smart/b_up.png" alt="ToTop">
</div>