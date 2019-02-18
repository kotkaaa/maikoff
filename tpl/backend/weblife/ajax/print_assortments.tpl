<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
    <{if !empty($arrPageData.errors)}>
        <{$arrPageData.errors|@implode:'<br/>'}>
    <{elseif !empty($arrPageData.messages)}>
        <{$arrPageData.messages|@implode:'<br/>'}>
    <{/if}>
</div>

<{if !empty($item.arAssortment)}>
<div class="logo-editing-area">        
    <form method="post" action="<{$arrPageData.current_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" enctype="multipart/form-data">
        <input type="hidden" name="printID" value="<{$arrPageData.printID}>"/>
        <input type="hidden" name="order"   value="<{$item.order}>"   />

        <h2>Параметры логотипа</h2><br/>
        <div style="border-bottom:1px solid #888;">
            <table>
                <tr>
                    <td valign="top">
                        <p class="reset-margin"><strong>Изображение</strong></p>
                        <input id="uploadedFile" type="file" name="filename"/> 
                        <input id="deleteLogo" type="hidden" name="logo_delete" value="0"/>
                        <div class="sizes">
                            <{if $item.arLogoSizes}>
                                width: <{$item.arLogoSizes.w}>px,<br/>
                                height: <{$item.arLogoSizes.h}>px
                            <{/if}>
                        </div>
                    </td>
                    <td valign="middle" align="center">
                        <div class="preview">
                            <!-- span и img должны стоять без пробела, чтобы изображение центрировалось корректно -->
                            <span class="helper"></span><img src="<{$item.filename}>" alt="выберите файл" align="center"/>
                        </div>
                    </td>
                    <td width="50"></td>
                    <td valign="top">
                        <p class="reset-margin"><strong>Название</strong></p>
                        <input type="text" name="title" value="<{$item.title}>" size="30"/>
                    </td>
                </tr>
            </table>
            <br/><br/>
        </div> 
        <{if $arrPageData.task=='addItem'}>
            <div class="tip"><br/><b>Для редактирования ассортимента сперва выберите логотип</b></div>
        <{/if}>
        <div class="assortment<{if $arrPageData.task=='addItem'}> hidden_block<{/if}>">
            <br/><h2>Ассортимент</h2><br/><br/>
            <{foreach from=$item.arAssortment key=substrateID item=arAssort}>
                <{assign var='iName' value='arAssortment['|cat:$substrateID|cat:']'}>
                <div class="substrates substrate-<{$substrateID}>">
                    <input type="hidden" name="<{$iName}>[id]" value="<{$arAssort.id}>"/>                    
                    <input type="hidden" name="<{$iName}>[title]" value="<{$arAssort.title}>"/>
                    <input type="hidden" name="<{$iName}>[isdefault]" value="<{$arAssort.isdefault}>"/>    
                    <input type="hidden" name="<{$iName}>[arSettings][id]" value="<{$arAssort.arSettings.id}>"/>
                    <input type="hidden" name="<{$iName}>[arSettings][active]" value="0"/>  
                    <label style="font-size: 16px">                        
                        <input type="checkbox" name="<{$iName}>[arSettings][active]" value="1" class="substrate-toogle"<{if $arAssort.arSettings.active>0}> checked="true"<{/if}>/>  
                        <b><{$arAssort.title}></b><{if $arAssort.isdefault}> - по умолчанию<{/if}>
                    </label>
                    <div class="assort<{if $arAssort.arSettings.active==0}> hidden_block<{/if}>">
                        <br/><br/>
                        <table>
                            <tr>
                                <td width="300" valign="middle" align="center">
                                    <div class="substrate">
                                        <div class="logo-preview"><img src="<{$item.filename}>" alt="логотип"/></div>
                                        <div class="substrate-preview"><img src="<{$arrPageData.substrate_url}><{$arrPageData.arEnabledColors[$substrateID][$arAssort.color_id].filename}>"/></div>
                                    </div>
                                </td>

                                <td width="260" valign="top">                                      
                                    <b>Размер лого, px</b><br/>
                                    <input class="width" type="text" name="<{$iName}>[arSettings][width]" size="7" value="<{$arAssort.arSettings.width}>" data-default="<{$arAssort.arSettings.width}>" data-value="<{$arAssort.arSettings.width}>"/> &nbsp;
                                    <img src="/images/operation/padlock.png" align="top" width="26"/> &nbsp;
                                    <input class="height" type="text" name="<{$iName}>[arSettings][height]" readonly size="7" value="<{$arAssort.arSettings.height}>" data-value="<{$arAssort.arSettings.height}>"/>
                                    <br/><br/>

                                    <b>Позиция лого, px</b><br/>
                                    <input class="offset" type="text" name="<{$iName}>[arSettings][offset]" size="7" value="<{$arAssort.arSettings.offset}>" data-value="<{$arAssort.arSettings.offset}>" data-default="<{$arAssort.arSettings.offset}>" />                                
                                </td>

                                <td width="300" valign="top">
                                    <label style="padding-left:16.5px;"><input type="checkbox" class="check-all"/> <b>Все цвета</b></label><br/>

                                    <ul>
                                    <{foreach from=$arrPageData.arEnabledColors[$substrateID] item=arColor name=c}>   
                                        <li class="color color-box color-<{$arColor.id}>" data-id="<{$arColor.id}>" data-filename="<{$arColor.filename}>" data-width="<{$arColor.width}>" data-height="<{$arColor.height}>">                                                                                                                                        
                                            <input class="default-color" type="radio" name="<{$iName}>[color_id]" value="<{$arColor.id}>"<{if $arAssort.color_id==$arColor.id}> checked="true"<{/if}>/>                                         
                                            <label>
                                                <input class="color-input" type="checkbox" name="<{$iName}>[colors][]" value="<{$arColor.id}>"<{if in_array($arColor.id, $arAssort.colors)}> checked="true"<{/if}>/>                                                 
                                                <div class="preview" style="background:#<{$arColor.hex}>;"></div> 
                                                <{$arColor.hex}>
                                            </label>
                                            <a href="javascript:;" class="update-logo"><img src="<{$arrPageData.system_images}>update.png"/></a>
                                        </li>
                                        <{if $smarty.foreach.c.iteration%6==0}></ul><ul><{/if}>
                                    <{/foreach}>
                                    </ul>

                                    <{if !empty($arrPageData.arDisabledColors[$substrateID])}>
                                        <br/>
                                        <div style="text-align:left">Цвета, задействованные в других логотипах:<br/>
                                            <{foreach from=$arrPageData.arDisabledColors[$substrateID] item=colorID}>
                                                <div class="color-box">
                                                    <div class="preview" style="background:#<{$arrPageData.arColors[$colorID].hex}>;"></div> 
                                                    <{$arrPageData.arColors[$colorID].hex}>
                                                </div>
                                            <{/foreach}>
                                        </div>
                                    <{/if}>
                                </td>
                            </tr>
                        </table><br/>
                    </div>
                </div>
            <{/foreach}>
            <br/><br/>
            <center>
                <input class="buttons" name="submit" type="submit" value="Сохранить" style="display:inline-block;"/>
                <input class="buttons" type="button" value="Обновить/Сбросить" onclick="location.reload();" style="display:inline-block;"/>
            </center>
        </div>    
    </form>
</div>
<script type="text/javascript">  
    $(function(){
        LogoManager.init();        
        parent.window.hs.onDimmerClick = function() {
            return false;
        };        
    });
    
    LogoManager = {        
        fileInput: '#uploadedFile',
        fileSrc: '<{if $item.filename}><{$item.filename}><{/if}>',
        logoWidth: <{if $item.arLogoSizes}><{$item.arLogoSizes.w}><{else}>0<{/if}>,
        logoHeight: <{if $item.arLogoSizes}><{$item.arLogoSizes.h}><{else}>0<{/if}>,  
        fileExtensions: ['png'],
        currentUrl: window.URL || window.webkitURL,
        substratesClass: '.substrates',
        assortmentClass: '.assortment',        
        previewClass: '.preview',
        sizesClass: '.sizes',
        tipClass: '.tip',
        widthClass: '.width',
        offsetClass: '.offset',
        logoPreviewClass: '.logo-preview',
        colorInputClass: '.color-input',
        defaultColorClass: '.default-color',
        colorClass: '.color',
        updateLogoClass: '.update-logo',
        toogleSubstrateClass: '.substrate-toogle',
        substratePreviewClass: '.substrate-preview',
        heightClass: '.height',
        checkAllClass: '.check-all',
        defaultSubstrateClass: '.default-substrate',  
        dir: '<{$arrPageData.substrate_url}>',
        
        init: function() {
            var _self = this;
            
            //событие на выбор файла
            $(_self.fileInput).change(function() {
                var selectedFile = $(this)[0].files[0],
                    preloadedImage = new Image();                    
                //валидируем формат файла                
                if($.inArray(selectedFile.name.split('.').pop(), _self.fileExtensions) >= 0) {
                    preloadedImage.src = _self.currentUrl.createObjectURL(selectedFile);
                    preloadedImage.onload = function() {
                        _self.fileSrc = preloadedImage.src;
                        _self.logoWidth = parseInt(this.width);
                        _self.logoHeight = parseInt(this.height);
                        //прячем ошибку, показываем ассортимент и размеры
                        $(_self.tipClass).addClass('hidden_block');
                        $(_self.assortmentClass).removeClass('hidden_block');                        
                        $(_self.sizesClass).html('width: '+_self.logoWidth+'px<br/>height: '+_self.logoHeight+'px'); 
                        //устанавливаем превью
                        $(_self.previewClass).find('img').attr('src', _self.fileSrc);
                        //для всех открытых типов накладываем изображение
                        $.each($(_self.substratesClass), function(index, container) { 
                            if(!$(container).is('visible')) _self.initsubstrate($(container).find(_self.substratePreviewClass));
                            //вставляем изображение
                            $(container).find(_self.logoPreviewClass).find('img').attr('src', _self.fileSrc);
                            _self.setPositions(container);   
                            _self.placeLogo(container);
                        });
                        $('#deleteLogo').val(1);
                        parent.window.hs.getExpander().reflow();
                    } 
                //формат не тот - выводим ошибку 
                } else {
                    alert('Ошибка! Формат файла не соответствует допустимым! Допустимые формат/ы - '+_self.fileExtensions.join(','));
                    _self.fileSrc = '';
                    //очищаем превью
                    $(_self.previewClass).find('img').attr('src', '');
                    //сбрасываем выбранный файл
                    $(_self.fileInput).val('');
                    //сбрасываем размеры, показываем ошибку, прячем ассортимент
                    $(_self.sizesClass).html('');
                    $(_self.tipClass).removeClass('hidden_block');
                    $(_self.assortmentClass).addClass('hidden_block'); 
                    //заполненные данные??????????????????????????????????????????????????????????????????????????????????????????????????????????????????
                }
                parent.window.hs.getExpander().reflow();
            });
            
            //переразмериваем окно
            if(typeof parent.window.hs.getExpander() != "undefined" && parent.window.hs.getExpander() !== null) parent.window.hs.getExpander().reflow();            
                      
            //для всех открытых типов пересчитываем размеры и накладываем изображение
            $.each($(_self.substratesClass+':visible'), function(index, container) {
                //инитим подложку
                _self.initsubstrate($(container).find(_self.substratePreviewClass));
                //перепроверяем размеры
                //_self.setPositions(container);
                //помещаем логотип
                _self.placeLogo(container);
            });
            
            //изменение высоты и отступа
            $(_self.widthClass+', '+_self.offsetClass).bind('input keydown focusout', function(e) {
                var input = $(this);
                var value = parseInt($(input).val())||0;
                var substrate = $(input).closest(_self.substratesClass).find(_self.substratePreviewClass);
                //проверяем кнопки
                if (typeof e.keyCode != "undefined" && (e.keyCode === 38 || e.keyCode === 40)) {                     
                    var newvalue = value + (e.keyCode === 40 ? -1 : 1); 
                    if(_self.isValidPosition(newvalue, input, substrate)) {
                        value = newvalue;
                    }
                }
                //проверяем изменилось ли че    
                if(value != $(input).data('value')) {
                    if(!$(input).is(':focus') && value.length == 0) {
                        value = parseInt($(input).data('default'));
                    }
                } 
                if (value.length != 0) {     
                    if(!_self.isValidPosition(value, input, substrate)) {
                        value = parseInt($(input).data('default'));
                    }
                    $(input).val(value);
                    $(input).data('value', value);
                    _self.placeLogo($(input).closest(_self.substratesClass));
                }
            });
            
            //нельзя снять с цвета галочку, если он установлен как дефолтный
            $(_self.colorInputClass).change(function(){
                var input = $(this);
                var color = $(input).closest(_self.colorClass);
                var substrate = $(input).closest(_self.substratesClass).find(_self.substratePreviewClass);
                var container = $(this).closest(_self.substratesClass);
                //если выбран он дефолтным, то не может быть отключен или если выбран только он один
                /*if($(color).find(_self.defaultColorClass).prop('checked') || $(container).find(_self.colorInputClass+':checked').length == 0) {
                    $(input).prop('checked', 'checked');
                }*/
                //при отключении цвета, если он был выбран, то перерисовываем подложку
                if($(input).prop('checked') == false && $(substrate).data('color_id') == $(color).data('id')) {
                    //переиничиваем подложку на дефолтный цвет
                    $(substrate).data('color_id', 0);
                    _self.initsubstrate(substrate);
                    _self.placeLogo($(input).closest(_self.substratesClass));
                }                
            });
            
            //если отмечаем цвет как дефолтный, то он сразу должен включиться
            $(_self.defaultColorClass).change(function() {
                var input = $(this);
                if($(input).prop('checked')) {
                     $(input).closest(_self.colorClass).find(_self.colorInputClass).prop('checked', 'checked');
                     //Отключаем все, кроме этого
                     $(input).closest(_self.substratesClass).find(_self.defaultColorClass).not(input).removeProp('checked');               
                }
                //если это единстенный дефолтный, то его низзя отключить
                /* 
                else if($(input).closest(_self.substratesClass).find(_self.defaultColorClass+':checked').not(input).length == 0) {
                    $(input).prop('checked', true);
                }
                */
            });
            
            //обновления логотипа - предпросмотр цвета
            $(_self.updateLogoClass).click(function() {
                var color = $(this).closest(_self.colorClass);
                var container = $(this).closest(_self.substratesClass);
                //чекаем цвет
                $(color).find(_self.colorInputClass).prop('checked', 'checked');
                //инитм новый цвет то есть новую подложку
                _self.initsubstrate($(container).find(_self.substratePreviewClass), color);
                //помещаем логотип
                _self.placeLogo(container);
            });
            
            //закрытие/открытие типа
            $(_self.toogleSubstrateClass).change(function() {
                _self.toogleSubstrate(this);
            });
            
            //выбрать все
            $(_self.checkAllClass).change(function() {
                var input = $(this);
                //меняем чекед и триггерим событие
                if($(input).prop('checked')) {
                    $(input).closest('td').find(_self.colorInputClass+':enabled').prop('checked', 'checked').change(); 
                } else {
                    $(input).closest('td').find(_self.colorInputClass+':enabled').removeProp('checked').change();
                }
            });
                        
            //проверка на 1 дефолтный
            $(_self.defaultSubstrateClass).change(function() {
                var input = $(this);
                if($(input).prop('checked')) {
                    //Отключаем все, кроме этого
                    $(_self.defaultSubstrateClass).not(input).removeProp('checked');
                }
            });                        
        },
                                
        initsubstrate: function(substrate, color) {
            var _self = this;
            var container = $(substrate).closest(_self.substratesClass);
            //если не передан цвет и еще не определен в подложке, то ищем дефолтный цвет
            if(typeof color == "undefined") {
                //если есть в подложке, то берем из нее
                if($(substrate).data('color_id')) {
                    color = $(container).find(_self.colorClass+'.color-'+$(substrate).data('color_id'));               
                //иначе пробуем достать дефолтный
                } else if($(container).find(_self.defaultColorClass+':checked').length>0) {
                    color = $(container).find(_self.defaultColorClass + ':checked').closest(_self.colorClass); 
                //иначе первый чекнутый
                } else if($(container).find(_self.colorInputClass+':checked').length>0) {
                    color = $(container).find(_self.colorInputClass + ':checked:first').closest(_self.colorClass); 
                //иначе просто первый
                } else {
                    color = $(container).find(_self.colorInputClass + ':first').closest(_self.colorClass); 
                }
            }            
            //проверяем нужно ли менять цвет ваще
            if(!$(substrate).data('color_id') || $(color).data('id') != $(substrate).data('color_id')) {
                //устанавливаем подложке айди цвета
                $(substrate).data('color_id', $(color).data('id'));
                //устанавливаем подложке изображение
                $(substrate).find('img').attr('src', _self.dir + $(color).data('filename'));
                //исходя из известных размеров подложки вычисляем коэфициент на который она уменьшилась, если нужно                       
                var coeff = 1;  //по умолчанию коєфициент 1
                var substrateWidth = parseInt($(color).data('width'));
                var previewWidth = parseInt($(substrate).width());
                //если размер помещенной подложки отличается от оригинального то определяем коефициент, на который изменился подложка
                if(previewWidth != substrateWidth) {
                    coeff = substrateWidth/previewWidth;    
                } 
                //устанавливаем подложке коэфициент
                $(substrate).data('coeff', coeff);
            }
        },
                                                
        placeLogo: function(container) {
            var _self = this;
            //определяем подложку
            var substrate = $(container).find(_self.substratePreviewClass); 
            //определяем логотип
            var logo = $(container).find(_self.logoPreviewClass);
            //получаем из подложки коэфициент сжатия         
            var coeff = parseFloat($(substrate).data('coeff')); 
            //устанавливаем размеры контейнера логотипа с учетом коэфициента
            $(logo).css({
                "width": parseInt($(container).find(_self.widthClass).val()/coeff),
                "top": parseInt($(container).find(_self.offsetClass).val()/coeff)
            });    
            //браузер уже за нас вычислил высоту, получаем ее, дает глупому firefox немного потупить
            setTimeout(function() {$(container).find(_self.heightClass).val(parseInt($(logo).height()*coeff))}, 200); 
        },
                
        toogleSubstrate: function(input) {
            var _self = this;
            var container = $(input).closest(_self.substratesClass);
            var items = $(container).find('.assort');
            if(input.checked) {
                $(items).removeClass('hidden_block');
                _self.initsubstrate($(container).find(_self.substratePreviewClass));
                _self.placeLogo(container);
            } else {
                if(!$(container).find(_self.defaultSubstrateClass).prop('checked')) {
                    $(items).addClass('hidden_block');
                } else {
                    $(input).prop('checked', true);
                }
            }
            parent.window.hs.getExpander().reflow();
        },
                
        getMaxWidth: function(color) {
            var _self = this;
            return parseInt(_self.logoWidth < $(color).data('width') ? _self.logoWidth : $(color).data('width'));
        },
        
        getMaxOffset: function(color) {
            return parseInt($(color).data('height'));
        },
            
        setPositions: function(container) {
            var _self = this;
            var substrate = $(container).find(_self.substratePreviewClass);
            var color = $(container).find(_self.colorClass+'.color-'+$(substrate).data('color_id'));
            var width = $(container).find(_self.widthClass);
            var offset = $(container).find(_self.offsetClass);
            if($(width).val() > _self.getMaxWidth(color)) {
                $(width).val(_self.getMaxWidth(color));
            }
            if($(offset).val() > _self.getMaxOffset(color)) {
                $(offset).val(_self.getMaxOffset(color));
            }
        },
        
        isValidPosition: function(value, input, substrate) {
            var _self = this;            
            var valid = false;            
            var container = $(substrate).closest(_self.substratesClass);            
            var color = $(container).find(_self.colorClass+'.color-'+$(substrate).data('color_id'));            
            if(value >= 0) {
                var maxValue = $(input).hasClass('width') ? _self.getMaxWidth(color) : _self.getMaxOffset(color);
                valid = (value <= maxValue);
            }            
            return valid;
        },
    }    
</script>
<{else}>
    <div class="logo-editing-area"><div class="tip"><br/><b>Все типы уже заняты! Тут нечего делать!</b></div></div>
<{/if}>