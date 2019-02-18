<div class="ring-me" id="how-much">
    <div class="container">
        <h3>Узнать стоимость печати</h3>
        <p>Оставьте свое имя и телефон и мы в ближайшее время сообщим стоимость печати</p>
        <h3 class="thank hidden">Спасибо за заявку</h3>
        <p class="thank hidden">В ближайшее время наш менеджер свяжется с вами</p>
        <form action="<{include file="core/href.tpl" arCategory=$arrModules.callback}>" method="POST" class="feedback-form-inline">
            <div class="flex">
                <input type="text" name="firstname" class="" placeholder="Имя">
                <input type="tel" name="phone" class="phone inputmask" placeholder="Телефон">
                <button class="btn btn-warning btn-xl">Сделать просчет</button>
            </div>
        </form>
    </div>
</div>