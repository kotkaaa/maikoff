<div class="form">
    <div class="container">
        <form action="<{include file="core/href.tpl" arCategory=$arrModules.callback}>" method="POST" class="callback-form-inline">
            <p class="info responsive">Введите номер телефона для того, чтобы мы смогли связаться с вами</p>
            <div class="flex">
                <input type="tel" name="phone" placeholder="+38">
                <button type="submit" class="btn btn-warning">Сделать просчет</button>
            </div>
            <p class="info hide">Введите номер телефона для того, чтобы мы смогли связаться с вами</p>
            <p class="info thanks hidden">Спасибо за заявку. В ближайшее время наш менеджер свяжеться с вами.</p>
        </form>
    </div>
</div>