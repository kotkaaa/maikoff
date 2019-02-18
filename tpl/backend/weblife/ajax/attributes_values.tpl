<{if $arrPageData.module == 'attributes_values'}>
<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
    <{if !empty($arrPageData.errors)}>
        <{$arrPageData.errors|@implode:'<br/>'}>
    <{elseif !empty($arrPageData.messages)}>
        <{$arrPageData.messages|@implode:'<br/>'}>
    <{/if}>
</div>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" enctype="multipart/form-data">
    <table class="list colored" width="100%">
        <tr>
            <td style='padding: 10px'>
<{/if}>
                <br/><strong>Настройка допустимых значений</strong><br/><br/>
                <div id="tip"></div>
                <div class="left">
                    <input id="attrValue" style="margin-top:5px; height:24px; padding-left:5px;" type="text" value="" placeholder="введите значение" class="nosize_field" size="96"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" class="buttons" value="Добавить" onclick="addAttrValue();" style="display:inline-block"/>
                </div>
                <div class="clear"></div> 
                <br/>
                <a href="javascript:void(0)" onclick="removeAttrVal(this, 'all');">Очистить список</a>
                <br/>
                <div class="sortable-wrapper" style="width:100%;">
                    <ul class="sortable" id="defaultVals">
<{foreach name=i from=$item.arValues key=arKey item=arValue}>
                        <li class="ui-state-default attrsort">
                            <input type="hidden" name="arValues[<{$arKey}>][id]" value="<{$arKey}>"/>
                            <input class="field main" type="text" name="arValues[<{$arKey}>][title]" value="<{$arValue.title}>" style="width: 150px;" title="недоступно для редактирования, так как используется в товаре"/>
                            <img class="more" src="/images/admin/more.png" align="middle"/>
                            <div class="variations">
                                <label>Single</label>
                                <input type="text" class="field" name="arValues[<{$arKey}>][title_single]" value="<{$arValue.title_single}>" style="width: 150px;">
                                <br>
                                <br>
                                <label>Multi</label>
                                <input type="text" class="field" name="arValues[<{$arKey}>][title_multi]" value="<{$arValue.title_multi}>" style="width: 150px;">
                                <br>
                                <br>
                                <label>Male</label>
                                <input type="text" class="field" name="arValues[<{$arKey}>][title_male]" value="<{$arValue.title_male}>" style="width: 150px;">
                                <br>
                                <br>
                                <label>Female</label>
                                <input type="text" class="field" name="arValues[<{$arKey}>][title_female]" value="<{$arValue.title_female}>" style="width: 150px;">
                                <br>
                                <br>
                                <label>Extra</label>
                                <input type="text" class="field" name="arValues[<{$arKey}>][title_extra]" value="<{$arValue.title_extra}>" style="width: 150px;">
                            </div>
                            <input class="field" type="text" name="arValues[<{$arKey}>][seo_path]" value="<{$arValue.seo_path}>" style="width: 150px;" <{if $arValue.used}>readonly title="недоступно для редактирования, так как используется в товаре"<{/if}>/>
                            <input type="button" value="Генерировать" style="margin: 3px 5px 0 0; min-width: 100px; display: inline-block;" class="buttons" onclick="if(this.form['arValues[<{$arKey}>][title]'].value.length==0){alert('Вы не ввели значение атрибута!'); this.form['arValues[<{$arKey}>][title]'].focus(); return false; } else{ generateSeoPath(this.form['arValues[<{$arKey}>][seo_path]'], this.form['arValues[<{$arKey}>][title]'].value, '<{$item.seo_path}>');}">
                            <input type="file" name="arValues[<{$arKey}>][image]" value="" style="margin-top:4px; width: 160px;"/>
<{if !empty($arValue.image)}>
                                <img src="<{$arrPageData.files_url|cat:$arValue.image}>" style="max-width:20px; max-height:20px;"/>
                                <input type="checkbox" name="arValues[<{$arKey}>][delete_image]" value="1"/> удалить 
<{/if}>
<{if !$arValue.used}>
                                <a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);"><img src="images/admin/error.png"/></a>&nbsp;
<{/if}>
                            <img class="right" src="images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" <{if $arValue.used}>readonly title="недоступно для редактирования, так как используется в товаре" style="margin-right:35px;"<{/if}>/>
                            <div class="clear"></div>
                        </li>
<{/foreach}>
                    </ul>
                </div>
<{if $arrPageData.module == 'attributes_values'}>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input class="buttons" name="submit" type="submit" value="Сохранить">
            </td>
        </tr>
    </table>
</form>
<{/if}>
<script type="text/javascript">    
    $(function() {
        $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
        $(document).keypress(function(e){
            if (e.which == 13 && $('#attrValue').val().length>0){
                addAttrValue();
                return false;
            }
        });
        $('#attrValue').autocomplete({
            source: function(request, response) {
                var arrValues = {};
                $.each($('ul.sortable').find('li').find('.field.main'), function() {
                    var value = $(this).val().toLowerCase();
                    if(value.indexOf(request.term.toLowerCase())!=-1) {
                        arrValues[$(this).attr('name')] = value;
                    }
                });
                response($.map(arrValues, function(item, i) {
                    return {
                        label: item,
                        value: item,
                        name: i
                    }
                }));
            },
            select: function(event, ui) {
                $.each($('ul.sortable').find('li').find('.field'), function() {
                    var input = $(this);                    
                    if($(input).val().toLowerCase() == ui.item.value.toLowerCase()) {
                        $(input).focus();
                    }
                });
                $(this).val("");
                return false;
            },
            minLength: 2
        });

        $('.sortable').on("click", ".more", function(){
            var ul = $(this).closest('ul'),
                li = $(this).closest('li');
            li.siblings('li').find(".clicked").removeClass("clicked");
            $(this).toggleClass('clicked');
        });
    });
    
    function addAttrValue() {
        if( $('#attrType option:selected').val()==2 && !isNumber($('#attrValue').val())){
            $('#tip').text('Введите число или измените тип на "Текстовый"');
            $('#attrValue').addClass('error');
            return false;
        } else {
            if($('#attrValue').val().length>0) {
                $('#attrValue').removeClass('error');
                var maxID = <{if isset($item.arValuesMaxID)}><{$item.arValuesMaxID}> + <{/if}>$('ul.sortable').find('li').length;
                var html = '<li class="ui-state-default attrsort">'+
                           '<input type="hidden" name="arValues['+maxID+'][id]" value=""/>'+
                           '<input name="arValues['+maxID+'][title]" class="field" type="text" value="'+$('#attrValue').val()+'" style="width: 150px;"/>'+                          
                           '<img class="more" src="/images/admin/more.png" align="middle"/>'+
                           '<div class="variations">'+
                           '<label>Single</label>'+
                           '<input type="text" class="field" name="arValues['+maxID+'][title_single]" style="width: 150px;">'+
                           '<br>'+
                           '<br>'+
                           '<label>Multi</label>'+
                           '<input type="text" class="field" name="arValues['+maxID+'][title_multi]" style="width: 150px;">'+
                           '<br>'+
                           '<br>'+
                           '<label>Male</label>'+
                           '<input type="text" class="field" name="arValues['+maxID+'][title_male]" style="width: 150px;">'+
                           '<br>'+
                           '<br>'+
                           '<label>Female</label>'+
                           '<input type="text" class="field" name="arValues['+maxID+'][title_female]" style="width: 150px;">'+
                           '<br>'+
                           '<br>'+
                           '<label>Extra</label>'+
                           '<input type="text" class="field" name="arValues['+maxID+'][title_female]" style="width: 150px;">'+
                           '</div>'+
                           '<input name="arValues['+maxID+'][seo_path]" class="field" type="text" style="width: 150px;"/>'+
                           '<input type="button" value="Генерировать" style="margin: 3px 5px 0 0; min-width: 100px; display: inline-block;" class="buttons" onclick="if(this.form[\'arValues['+maxID+'][title]\'].value.length==0){alert(\'Вы не ввели значение атрибута!\'); this.form[\'arValues['+maxID+'][title]\'].focus(); return false; } else{ generateSeoPath(this.form[\'arValues['+maxID+'][seo_path]\'], this.form[\'arValues['+maxID+'][title]\'].value, \'<{$item.seo_path}>\');}">'+
                           '<input type="file" name="arValues['+maxID+'][image]" value="" style="margin-top: 4px; width: 160px;"/>'+ 
                           '<a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);">'+
                           '<img src="images/admin/error.png"/></a>'+
                           '<img class="right" title="Нажмите и перетащите элемент на новое место в списке" src="images/sort.png"/>'+
                           '<div class="clear"></div>'+
                           '</li>';                           
                $('ul.sortable').prepend(html);  
                $('#attrValue').val('');
                $('#tip').text('');
                $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
            }
        }   
    }       
    function removeAttrVal(item, removeAll) {
        if (typeof removeAll == 'undefined')
            $(item).parent().remove();
        else 
            $('ul.sortable').html('');
    }
</script>