<{if !$IS_DEV}>
<script type="text/javascript">
/* <![CDATA[ */
    var google_conversion_id = 978629849,
        google_custom_params = window.google_tag_params,
        google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/978629849/?value=0&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s){
        if (f.fbq) return;
        n=f.fbq=function(){
            n.callMethod ? n.callMethod.apply(n,arguments):n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq=n;
        n.push=n;
        n.loaded=!0;
        n.version='2.0';
        n.queue=[];
        t=b.createElement(e);
        t.async=!0;
        t.src=v;
        s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)
    }(window,document,'script', 'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1955390581154801');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" src="https://www.facebook.com/tr?id=1955390581154801&ev=PageView&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
<!--{/if}-->
<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'WpveYdFCJe';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>
<!-- {/literal} END JIVOSITE CODE -->
<{/if}>
<{foreach from=$arrPageData.headCss item=style}>
        <link href="<{$style}>" type="text/css" rel="stylesheet"/>
<{/foreach}>