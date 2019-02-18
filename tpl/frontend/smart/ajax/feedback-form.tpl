<{* в popup окне *}>
<{if $arrPageData.ajax}>
<{if !empty($arrPageData.messages)}>
<div class="modal-order-form-result">
    <hgroup>
        <h1>Спасибо</h1>
        <h2>Ваше обращение принято</h2>
    </hgroup>
    В ближайшее время мы ответим на ваш вопрос
    <div class="f-submit">
        <button class="btn btn-primary btn-xl" onclick="Modal.open('<{include file="core/href.tpl" arCategory=$arrModules.feedback params="ajax="|cat:"1"}>');">Задать новый вопрос</button>
        <button class="btn btn-link btn-xl" onclick="Modal.close();">Закрыть окно</button>
    </div>
</div>
<script type="text/javascript">
    // send GA event
    send_event("Успешно отправлено", "Форма просчета");
</script>
<{else}>
<div class="modal-order-form">
    <div class="hgroup">
        <h2>Связаться с нами</h2>
        По любым вопросам вы можете писать в форму.
        Все вопросы и предложения по сотрудничеству будут рассмотрены и мы ближайшее время ответим вам.
    </div>
    <div class="form">
        <form action="<{include file="core/href.tpl" arCategory=$arrModules.feedback params="ajax="|cat:"1"}>" method="POST" id="mFeedbackForm">
            <div class="f-row f-row-flex main-form-fields">
                <div class="f-col">
                    <label class="f-label">* Ваше имя</label>
                    <input type="text" name="firstname" class="<{if isset($arrPageData.errors.firstname)}>error<{/if}>" value="<{if isset($item.firstname)}><{$item.firstname}><{/if}>"/>
                </div>
                <div class="f-col">
                    <label class="f-label">* Телефон для обратной связи</label>
                    <input type="tel" name="phone" placeholder="+38" class="<{if isset($arrPageData.errors.phone)}>error<{/if}>" value="<{if isset($item.phone)}><{$item.phone}><{/if}>"/>
                </div>
                <div class="f-col">
                    <label class="f-label">E-mail для обратной связи</label>
                    <input type="email" name="email" class="<{if isset($arrPageData.errors.email)}>error<{/if}>" value="<{if isset($item.email)}><{$item.email}><{/if}>"/>
                </div>
            </div>
            <div class="f-comment">
                <label class="f-label" for="message">Комментарий</label><br>
                <textarea id="message" name="message" style="display: block;max-width: 100%;" class="<{if isset($arrPageData.errors.message)}>error<{/if}>"><{if isset($item.message)}><{$item.message}><{/if}></textarea>
            </div>
            <div class="f-submit">
                <button class="btn btn-warning btn-xxl" type="submit">Отправить</button>
                <button class="btn btn-link btn-xl" type="reset" onclick="Modal.close();">Отмена</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function init_mFeedbackForm(timeout){
        if (typeof jQuery != "undefined") {
            $(function(){
                var $form = $("#mFeedbackForm"),
                    $target = $form.closest(".response");
                $form.ajaxForm({
                    target: $target,
                    success: function(){
                        Modal.draw();
                    }
                });
            });
        } else {
            setTimeout(function(){
                init_mFeedbackForm(timeout);
            }, timeout);
        };
    }; init_mFeedbackForm(100);
</script>
<{/if}>
<{* на странице Контакты (не в popup окне) *}>
<{else}>
<{if !empty($arrPageData.messages)}>
<div class="thanks">
    <div class="icon"></div>
    <div class="thanks-info">
        <h2>Спасибо, Ваш вопрос отправлен,<br>мы ответим на него в ближайшее время</h2>
        <p>
            <a href="#" onclick="window.location.reload();">Задать новый вопрос</a>
        </p>
    </div>
</div>
<script type="text/javascript">
    // send GA event
    send_event("Успешно отправлено", "Контакты");
</script>
<{else}>
<div class="contact-adress">
    <p class="home"><{$objSettingsInfo->ownerAddress|unScreenData|strip_tags|nl2br}></p>
    <p class="clock"><{$objSettingsInfo->schedule|unScreenData|strip_tags|nl2br}></p>
<{foreach name=i from=$HTMLHelper->getContactPhones($objSettingsInfo->sitePhone) item=phone}>
    <p class="contact-phone <{if $smarty.foreach.i.first}>phone<{/if}>">
        <a href="tel:<{$phone.tel}>"><{$phone.num}></a>
    </p>
<{/foreach}>
    <p class="contact-email">
        <a href="mailto:<{$objSettingsInfo->siteEmail}>"><{$objSettingsInfo->siteEmail}></a>
    </p>
</div>
<form action="" method="POST" id="FeedbackForm">
    <div class="feedback">
        <label for="name">* Ваше имя</label><br>
        <input type="text" name="firstname" id="name" class="name <{if isset($arrPageData.errors.firstname)}>error<{/if}>" value="<{if isset($item.firstname)}><{$item.firstname}><{/if}>"/><br>
        <label for="phone">* Телефон для обратной связи</label><br>
        <input type="tel" name="phone" id="phone" class="phone <{if isset($arrPageData.errors.phone)}>error<{/if}>" placeholder="+38" value="<{if isset($item.phone)}><{$item.phone}><{/if}>"><br>
        <label for="email">  E-mail для обратной связи</label><br>
        <input type="email" name="email" id="email" class="email <{if isset($arrPageData.errors.email)}>error<{/if}>" value="<{if isset($item.email)}><{$item.email}><{/if}>"/>
    </div>
    <div class="comment">
        <label for="comment"> Комментарий</label><br>
        <textarea id="comment" name="message" class="<{if isset($arrPageData.errors.message)}>error<{/if}>"><{if isset($item.message)}><{$item.message}><{/if}></textarea>
        <div class="submit">
            <div class="g-recaptcha" id="g_recaptcha" data-sitekey="<{$smarty.const.RECAPTCHA_SITE_KEY}>"></div>
            <br/>
            <button class="btn btn-primary btn-xl" disabled>Отправить</button>
        </div>
    </div>
 </form>
<{/if}>
<{/if}>