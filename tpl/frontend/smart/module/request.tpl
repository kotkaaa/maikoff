<{if !empty($arrPageData.messages)}>
<div class="modal-order-form-result">
    <hgroup>
        <h1>Спасибо за заявку!</h1>
        <h2>Начинаем просчет</h2>
    </hgroup>
    В течение 3-х часов сообщим точную цену и сроки изготовления
    <div class="f-submit">
        <button class="btn btn-primary btn-xl" onclick="Modal.open('<{include file="core/href.tpl" arCategory=$arrModules.request}>');">Просчитать другую одежду</button>
        <button class="btn btn-link btn-xl" onclick="Modal.close();">Закрыть окно</button>
    </div>
</div>
<{else}>
<div class="modal-order-form">
    <div class="hgroup">
        <h2>Просчет стоимости нанесения логотипа</h2>
        Для просчета стоимости печати вам необходимо оставить контакнтную информацию.
        Дополнительно вы можете приложить лого к форме, указать типы одежды, размеры, количество
        товаров и место нанесения на одежде.
    </div>
    <div class="form">
        <form action="<{include file="core/href.tpl" arCategory=$arrModules.request}>" method="POST" id="requestForm">
            <div class="f-row f-row-flex main-form-fields">
                <div class="f-col">
                    <label class="f-label">* Ваше имя</label>
                    <input type="text" name="firstname" class="<{if isset($arrPageData.errors.firstname)}>error<{/if}>"/>
                </div>
                <div class="f-col">
                    <label class="f-label">* Телефон для обратной связи</label>
                    <input type="tel" name="phone" placeholder="+38" class="<{if isset($arrPageData.errors.phone)}>error<{/if}>"/>
                </div>
                <div class="f-col">
                    <label class="f-label">E-mail для подтверждения макета</label>
                    <input type="email" name="email" class="<{if isset($arrPageData.errors.email)}>error<{/if}>"/>
                </div>
            </div>
            <div class="f-row attach-files">
                <label class="f-label">Загрузите свое лого и отправьте  нам для просчета печати</label>
                <input type="file" class="hidden" name="files[]" id="requestFileUpload" multiple/>
                <button class="btn btn-primary btn-xl btn-attach sm tooltips" onclick='$("#requestFileUpload").trigger("click"); return false;'>
                    <span class="pin"></span>
                    Вложить лого
                </button>
                <div class="attachments"></div>
                <div id="tooltip_attach_files">
                    Форматы файлов для загрузки: <{OrderHelper::UPLOAD_ALLOW_EXTENSION_TEXT}>.<br>
                    Не больше <{OrderHelper::MAX_FILES_COUNT}>-х изображений.<br>
                    Вес одного изображения не должен превышать <{OrderHelper::UPLOAD_MAX_FILESIZE_TEXT}> Мб.
                </div>
            </div>
            <div class="selected-items <{if !$show_products}>hidden<{/if}>">
                <div class="selected-item empty">
                    <button class="btn btn-primary btn-xl btn-add-new">Добавить новый тип товара</button>
                </div>
            </div>
            <div class="f-comment">
                <label class="toggle <{if !$show_products}>toggle-on permanent<{/if}>" <{if $show_products}>onclick="$(this).toggleClass('toggle-on')"<{/if}>></label>
                <textarea name="comment"></textarea>
            </div>
            <div class="f-submit">
                <button type="submit" class="btn btn-warning btn-xxl">Отправить просчет</button>
                <button type="reset" class="btn btn-link btn-xl" onclick="Modal.close();">Отмена</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        if (typeof RequestForm != "undefined") {
            RequestForm.init();
            RequestForm.setParams({
                product_substrates: <{$arrPageData.product_substrates|json_encode}>,
                product_colors:<{$arrPageData.product_colors|json_encode}>
            });
        }
    });
</script>
<{/if}>