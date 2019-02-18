<{if !isset($position)}><{assign var=position value="inline"}><{/if}>
<{if $position=="right"}>
<{if !empty($arrPageData.messages)}>
<div class="subscribe-result">
    <strong>Поздравляем!</strong><br>
    Вы успешно подписаны
</div>
<{else}>
<h2>Подписка на новости</h2>
<form action="<{include file="core/href.tpl" arCategory=$arrModules.subscribe params="position="|cat:$position}>" method="POST" id="qSubscribeForm">
    <input type="email" name="email" class="email-form" placeholder="Ваш e-mail"><br>
    <button class="btn btn-primary btn-xl">Подписаться</button>
    <script type="text/javascript">
        function init_qSubscribeForm(timeout){
            if (typeof jQuery != "undefined") {
                $(function(){
                    var $form = $("#qSubscribeForm"),
                        $input = $form.find("input[type=\"email\"]"),
                        $tooltip = $input.tooltip({
                            position: {
                                my: "left top-58",
                                at: "left top",
                                collision: "flipfit"
                            }
                        });
                        $input.on("mouseenter", function(){
                            $tooltip.tooltip("option", "disabled", true);
                            $tooltip.tooltip("option", "content", "");
                        });
                    $form.ajaxForm({
                        dataType: "json",
                        success: function(json){
                            if (json.errors) {
                                $input.prop("title", json.errors.email);
                                $tooltip.tooltip("option", "disabled", false);
                                $tooltip.tooltip("option", "content", json.errors.email);
                                $tooltip.tooltip("open");
                            } else if (json.messages && json.output) {
                                $form.replaceWith(json.output);
                            }
                        }
                    });
                });
            } else {
                setTimeout(function(){
                    init_qSubscribeForm(timeout);
                }, timeout);
            };
        }; init_qSubscribeForm(100);
    </script>
</form>
<{/if}>
<{/if}>