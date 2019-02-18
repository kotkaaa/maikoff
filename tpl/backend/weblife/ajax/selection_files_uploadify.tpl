<div id="right_block">
    <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
        <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
        <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
    </div>
    
    <table width="100%" cellspacing="5" cellpadding="0" border="0" class="sheet">        
        <tbody>
            <tr>
                <td width="400" id="head" align="center"><h3>Форма загрузки</h3></td>
                <td id="head" align="center"><h3>Загруженные файлы</h3></td>
            </tr>
            <tr>
                <td valign="top" align="center" class="upload-file-browser">
                    <button class="buttons btn-browse">Выбрать файл</button>
                    <input type="file" name="files[]" class="hidden" id="userFile"/>
                    <div class="messages hidden"></div>
                    <div class="preview">
                        
                    </div>
                    <div class="actions">
                        <button class="buttons inline btn-submit">Загрузить</button>
                        <button class="buttons inline btn-cancel">Отмена</button>
                    </div>
                </td>
                <td valign="top">
                    <br/>
                    <form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItems"}>" name="editItems" id="ajaxEditForm">
                        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list colored" id="operationTbl">
                            <thead>
                                <tr>
                                    <td id="headb" align="center" width="12">
                                        <input class="checkboxes check_all" type="checkbox" value="0" onchange="SelectCheckBox(this);"/>
                                    </td>
                                    <td id="headb" align="center" width="30"></td>
                                    <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                                    <td id="headb" align="left" width="200">Ссылка</td>
                                    <td id="headb" align="center" width="22"><{$smarty.const.HEAD_SORT}></td>
                                    <td id="headb" align="center" width="22"><{$smarty.const.HEAD_PUBLICATION}></td>
                                    <td id="headb" align="center" width="35"><{$smarty.const.HEAD_DELETE}></td>
                                </tr>
                            </thead>
                            <tbody>
<{section name=i loop=$items}>
                                <tr>
                                    <td class="inputs" align="center">
                                        <input type="checkbox" class="checkboxes" name="arItems[<{$items[i].id}>]" onchange="SelectCheckBox(this);" value="1" />
                                    </td>
                                    <td align="center">
                                        <a href="<{$arrPageData.files_url|cat:$items[i].filename}>" onclick="return parent.hs.expand (this, { })" class="highslide">
                                            <img src="<{$arrPageData.files_url|cat:$items[i].filename}>" alt="View" title="View" width="24" style="border:none;"/>
                                        </a>
                                    </td>
                                    <td>
                                        <input type="text" class="field" name="arData[<{$items[i].id}>][title]" value="<{$items[i].title}>" style="width:96%"/>
                                    </td>
                                    <td align="center">
                                        <input type="text" class="field" name="arData[<{$items[i].id}>][url]" value="<{$items[i].url}>" style="width:96%"/>
                                    </td>
                                    <td align="center">
                                        <input class="field_smal" name="arData[<{$items[i].id}>][order]" type="text" id="order" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
                                    </td>
                                    <td align="center">
<{if $items[i].active==1}>
                                        <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="Publication">
                                            <img src="<{$arrPageData.system_images}>check.png" alt="Publication" title="Publication" />
                                        </a>
<{else}>
                                        <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="No Publication">
                                            <img src="<{$arrPageData.system_images}>un_check.png" alt="No Publication" title="No Publication" />
                                        </a>
<{/if}>
                                    </td>
                                    <td align="center">
                                        <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('Вы уверены что хотите удалить файл?');" title="Delete!">
                                           <img src="<{$arrPageData.system_images}>delete.png" alt="Delete!" title="Delete!" />
                                        </a>
                                    </td>
                                </tr>
<{/section}>
                            </tbody>
                        </table>
                        <table width="100%" border="0" cellspacing="1" cellpadding="0">
                            <tr>
                                <td>
                                    <{$smarty.const.SITE_COUNT_RECORDS}><{$arrPageData.total_items}>
                                    <input type="submit" value="Применить изменения" class="buttons" style="display:inline-block;margin-left:150px"/>
                                </td>
                            </tr>
<{if $arrPageData.total_pages>1}>
                            <tr>
                                <td>
                                    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                                    <{include file='pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=1 showFirstLast=0 showPrevNext=0}>
                                    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                                </td>
                            </tr>
<{/if}>
                            <tr>
                                <td>
<{if $arrPageData.total_items>0}>
                                    Выбранные: <br/>
                                    <a href="javascript:void(0)" onclick="checkBoxOperations('publishItems', '1')">
                                        Опубликовать
                                    </a>
                                    <br/>
                                    <a href="javascript:void(0)" onclick="checkBoxOperations('publishItems', '0')">
                                        Не публиковать
                                    </a>
                                    <br/>
                                    <a href="javascript:void(0)" onclick="checkBoxOperations('deleteItems', '1')">
                                        Удалить
                                    </a>
<{/if}>
                                </td>
                                <td id="controls-box" >
                                    <a class="exit-button" href="javascript:void(0)" onclick="parent.window.hs.close();" title="<{$smarty.const.BUTTON_EXIT}>"></a>
                                    <a class="reload-button" href="javascript:void(0)" onclick="window.location.reload();" title="<{$smarty.const.BUTTON_RELOAD}>"></a>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var Table = $("#operationTbl"),
            Browser = $(".upload-file-browser"),
            preview = Browser.children(".preview"),
            messages = Browser.children(".messages");
        
        Browser.on("click", ".btn-submit", function(e){
            var input = preview.children("input"),
                iVal  = input.val();
            if (typeof iVal == "undefined") {
                preview.html("");
                messages.removeClass("hidden").text("Выберите файл для загрузки!!!");
            } else {
                $.ajax({
                    url: "<{$arrPageData.current_url|cat:"&task=ajaxSelectionFilesUpload"}>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        filename: iVal
                    },
                    success: function(json) {
                        if (json.errors) {
                            messages.removeClass("hidden").text(implode("<br/>", json.errors));
                        } else if (json.item) {
                            var row = "<tr>";
                                row+= "<td class=\"inputs\" align=\"center\">";
                                row+= "<input type=\"checkbox\" class=\"checkboxes\" name=\"arItems[" + json.item.id + "]\" onchange=\"SelectCheckBox(this);\" value=\"1\"/>";
                                row+= "</td>";
                                row+= "<td align=\"center\">";
                                row+= "<a href=\"<{$arrPageData.files_url}>" + json.item.filename + "\" onclick=\"return parent.hs.expand(this, {});\" class=\"highslide\">";
                                row+= "<img src=\"<{$arrPageData.files_url}>" + json.item.filename + "\" alt=\"View\" title=\"View\" width=\"24\" style=\"border:none;\"/>";
                                row+= "</a>";
                                row+= "</td>";
                                row+= "<td>";
                                row+= "<input type=\"text\" class=\"field\" name=\"arData[" + json.item.id + "][title]\" value=\"\" style=\"width:96%\"/>";
                                row+= "</td>";
                                row+= "<td align=\"center\">";
                                row+= "<input type=\"text\" class=\"field\" name=\"arData[" + json.item.id + "][url]\" value=\"\" style=\"width:96%\"/>";
                                row+= "</td>";
                                row+= "<td align=\"center\">";
                                row+= "<input class=\"field_smal\" name=\"arData[" + json.item.id + "][order]\" type=\"text\" value=\"" + json.item.order + "\" style=\"width:27px;padding-left:0px;text-align:center;\" maxlength=\"4\"/>";
                                row+= "</td>";
                                row+= "<td align=\"center\">";
                                row+= "<a href=\"<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="}>" + json.item.id + "\" title=\"Publication\">";
                                row+= "<img src=\"<{$arrPageData.system_images}>check.png\" alt=\"Publication\" title=\"Publication\"/>";
                                row+= "</a>";
                                row+= "</td>";
                                row+= "<td align=\"center\">";
                                row+= "<a href=\"<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="}>" + json.item.id + "\" onclick=\"return confirm('Вы уверены что хотите удалить файл?');\" title=\"Delete!\">";
                                row+= "<img src=\"<{$arrPageData.system_images}>delete.png\" alt=\"Delete!\" title=\"Delete!\"/>";
                                row+= "</a>";
                                row+= "</td>";
                                row+= "</tr>";
                            Table.children("tbody").append(row);
                            $("#messages").removeClass("errors").removeClass("hidden_block").addClass("info").text("Файл успешно загружен!!!");
                            preview.html("");
                        }
                    },
                    beforeSend: function(){
                        messages.addClass("hidden").html("");
                    }
                });
            }
        }).on("click", ".btn-cancel", function(e){
            preview.html("");
            messages.addClass("hidden").html("");
        }).on("click", ".btn-browse", function(e){
            $('#userFile').trigger("click");
        });
        
        $('#userFile').fileupload({
            url: "/interactive/ajax.php?zone=admin&action=ajaxFileUpload",
            dataType: 'json',
            acceptFileTypes: /(\.|\/)(jpe?g|png|gif)$/i,
            complete:function (result, textStatus, jqXHR) {
                var cnt = 0,
                    json = result.responseJSON;
                if (typeof json == "undefined" || result.status != 200) return false;
                $.each(json.files, function (index, file) {
                    if (cnt>=1) return;
                    var error = false,
                        maxFileSize = 10000 * 1024;
                    if (file.size > maxFileSize) error = "Превышен максимально допустимый размер файла (10Мб)!!!";
                    if (error) {
                        messages.removeClass("hidden").text(error);
                    } else {
                        var html  = "<input type=\"hidden\" value=\"" + file.name + "\"/>";
                            html += "<img src=\"<{$smarty.const.UPLOAD_URL_DIR}>temp/" + file.name + "\"/>";
                        preview.html(html);
                        cnt++;
                    }
                });
            },
            beforeSend: function(){
                messages.addClass("hidden").html("");
            }
        });
    });
    
    function checkBoxOperations(task, value){
        var inputs = $('#operationTbl').find("input[type=checkbox]:checked:not(#checkAll)"); 
        if(inputs.length > 0) {
            var data = '';
            $.each( inputs, function() {
                data += '&'+[$(this).attr("name")]+'='+value;
            }); window.location = "<{$arrPageData.current_url}>"+"&task="+task+data;
        }
    }
    
</script>
