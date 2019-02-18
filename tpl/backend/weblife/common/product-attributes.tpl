<table width="100%" border="0" cellspacing="0" cellpadding="0" class="sheet" id="attrTable">
    <thead>
        <tr>
            <td style="padding:5px;">
                <select id="attrList" onchange="Attributes.addGroup(this.value);" style="width: 300px;" class="field">
                    <option> -- Выберите группу атрибутов -- </option>
<{section name=i loop=$arrPageData.attrGroups}>
<{if !in_array($arrPageData.attrGroups[i].id, $item.attrGroups)}>
                    <option value="<{$arrPageData.attrGroups[i].id}>">
                        <{$arrPageData.attrGroups[i].title}> 
                    </option>
<{/if}>
<{/section}>
                </select>
            </td>
        </tr>
    </thead>
    <tbody>
<{section name=i loop=$arrPageData.attrGroups}>
<{if in_array($arrPageData.attrGroups[i].id, $item.attrGroups)}>
    <{include file="ajax/attributes_form.tpl" attrGroup=$arrPageData.attrGroups[i] selected=$item.attributes}>
<{/if}>
<{/section}>
    </tbody>
</table>

<script type="text/javascript">
    $(function(){
        Attributes.init();
    });
    var Attributes = {
        table: null,
        init: function(){
            var self = this;
            self.table = $('#attrTable');
            $.map(self.table.find('.attributes'), function(row) {
                self.initRow($(row));
            });
        },
        initRow: function (row) {
            var input    = row.find('.searchAttrValue'),
                init     = row.data('init')||false;
            if (!init) {
                $(input).select2();
                row.data('init', 1);
            }
        },
        addValue: function (holder, aid, value, label) {
            var html  = "<div class=\"attr\">";
                html += "<input type=\"hidden\" name=\"attributes[" + aid + "][]\" value=\"" + value + "\"/>";
                html += label + " <span onclick=\"$(this).parent().remove();\">X</span>";
                html += "</div>";
            $(holder).append(html);
        },
        addGroup: function (gid) {
            gid = parseInt(gid) || 0;
            var self = this,
                ArGroups = new Array();
            $.map(self.table.children('tbody').children('tr'), function(tr){
                ArGroups.push($(tr).data("gid"));
            });
            if (gid > 0) {
                $.ajax({
                    url: '/interactive/ajax.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        zone: 'admin',
                        action: 'addAttributeRow',
                        itemID: parseInt(<{$item.id}>),
                        groupID: gid,
                        arGroups: ArGroups
                    }, 
                    success: function(json) {
                        if (json.tpl) {
                            self.table.children('tbody').append(json.tpl);
                        }
                        if (json.select) {
                            self.updateSelectBox(json.select);
                        }
                        self.init();
                    }
                });
            }
        },
        removeGroup: function (gid) {
            var self   = this,
                target = self.table.children('tbody').children('tr#attrGroup_' + gid),
                select = self.table.find('#attrList'),
                arData = new Object();
            $.map(select.children('option'), function(opt) {
                var val = parseInt($(opt).val()) || 0,
                    title = $(opt).text();
                if (val > 0) arData[val] = title;
            });
            arData[gid] = $(target).data('title');
            $(target).remove();
            return self.updateSelectBox(arData);
        },
        updateSelectBox: function(data) {
            data = data||{};
            var self   = this,
                select = self.table.find('#attrList'),
                html   = '<option> -- Выберите группу атрибутов -- </option>';
            if (!empty(data)) {
                for (var id in data) {
                    html += '<option value="' + id + '">' + data[id] + '</option>';
                }
            }
            $(select).html(html);
            return true;
        }
    };
</script>