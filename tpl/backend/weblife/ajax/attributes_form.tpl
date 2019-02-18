<tr id="attrGroup_<{$attrGroup.id}>" data-title="<{$attrGroup.title}> <{if $attrGroup.descr}>(<{$attrGroup.descr}>)<{/if}>" data-gid="<{$attrGroup.id}>">
    <td>
        <strong><{$attrGroup.title}> <{if $attrGroup.descr}>(<{$attrGroup.descr}>)<{/if}></strong>
        <a href="javascript:void(0);" data-gid="<{$attrGroup.id}>" class="del" onclick="Attributes.removeGroup(<{$attrGroup.id}>);"><{$smarty.const.LABEL_DELETE}></a>
        <div style="clear: both; margin-bottom: 10px;"></div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list colored" style="font-weight:400">
<{foreach name=i from=$attrGroup.attributes item=attribute}>    
            <tr data-aid="<{$attribute.id}>">
                <td align="left" valign="middle" width="155" <{if $attribute.descr}>title="<{$attribute.descr}>"<{/if}> style="padding:5px">
                    <{$attribute.title}>
                    <a href="/admin.php?module=attributes_values&task=editItem&itemID=<{$attribute.id}>&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'Атрибуты: <{$attribute.title}>', objectType:'iframe', preserveContent: false, width:910});">
                        <img src="/images/operation/edit.png" height="10">
                    </a>
                </td>
                <td style="padding:5px;width:510px">
                    <div class="attributes" data-aid="<{$attribute.id}>">
                        <select name="attributes[<{$attribute.id}>][]" class="searchAttrValue" style="width: 500px;" multiple>
<{section name=l loop=$attribute.values}>
                            <option value="<{$attribute.values[l].id}>" <{if isset($selected) && array_key_exists($attribute.id, $selected) && in_array($attribute.values[l].id, $selected[$attribute.id])}>selected<{/if}>>
                                <{$attribute.values[l].title}>
                            </option>
<{/section}>
                        </select>
                        <div class="error-handler hidden_block"></div>
                    </div>
                </td>
                <td align="left">
                    {attribute_<{$attribute.id}>}
                </td>
            </tr>
<{/foreach}>
        </table>
    </td>
</tr>
