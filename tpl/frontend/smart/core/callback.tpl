<div class="callback-dropdown hidden">
    <div class="inner">
        <div class="list-phones ">
            <div class="schedule">Пн-Пт&nbsp;&nbsp;&nbsp;&nbsp;10<sup>00</sup>-19<sup>00</sup></div>
            <ul>
<{foreach name=i from=$HTMLHelper->getContactPhones($objSettingsInfo->sitePhone) item=phone}>
                <li>
                    <a href="tel:<{$phone.tel}>"><{$phone.num}></a>
                </li>
<{/foreach}>
            </ul>
            <div class="go">
                <button class="btn btn-primary btn-xl" onclick="Callback.go(2);">Перезвоните мне</button>
            </div>
        </div>
        <div class="form hidden">
            <form action="<{include file="core/href.tpl" arCategory=$arrModules.callback}>" id="qCallbackForm" method="POST">
                <label>Мой номер телефона</label>
                <input type="tel" name="phone" class="input-xl input-fx" placeholder="+38 ___ ___-__-__"/>
                <button class="btn btn-primary btn-xl" type="submit" disabled>Перезвонить</button>
                <button class="btn btn-link btn-xl" type="reset" onclick="Callback.go(1);">Отмена</button>
            </form>
        </div>
        <div class="result hidden">
            <p>Спасибо за заявку</p>
            В ближайшее время<br/>
            мы перезвоним Вам
        </div>
    </div>
</div>