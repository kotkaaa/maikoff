<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
<{if !empty($refresh.head)}>
        <{$refresh.head}>
<{/if}>
        <title><{$arrPageData.headTitle}></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta http-equiv="imagetoolbar" content="no">
        <link rel="stylesheet" type="text/css" href="<{$arrPageData.css_dir}>admin.css" />
        <link rel="shortcut icon" href="<{$arrPageData.images_dir}>weblife.ico" />
<{if $bannedTime>0}>
        <script type="text/javascript">
            <!--
            var banTimerID = null;
            function updateBanTimer(){
                var banDate = new Date(<{$bannedTime}>*1000); // seconds*1000 in JavaScripts Milisecons Timestamp
                var nowDate = new Date();
                if( banDate >= nowDate && document.getElementById('banTimer') != null &&
                    nowDate.getFullYear() == banDate.getFullYear() &&
                    nowDate.getMonth()    == banDate.getMonth() &&
                    nowDate.getDate()     == banDate.getDate() ) {
                        var totalRemains = (banDate.getTime()-nowDate.getTime());
                        var RemainsSec=(parseInt(totalRemains/1000));
                        var RemainsFullDays=(parseInt(RemainsSec/(24*60*60)));
                        var secInLastDay=RemainsSec-RemainsFullDays*24*3600;
                        var RemainsFullHours=(parseInt(secInLastDay/3600));
                        //if (RemainsFullHours<10){RemainsFullHours="0"+RemainsFullHours};
                        var secInLastHour=secInLastDay-RemainsFullHours*3600;
                        var RemainsMinutes=(parseInt(secInLastHour/60));
                        //if (RemainsMinutes<10){RemainsMinutes="0"+RemainsMinutes};
                        var lastSec=secInLastHour-RemainsMinutes*60;
                        //if (lastSec<10){lastSec="0"+lastSec};
                        document.getElementById('banTimer').innerHTML = /*RemainsFullHours+" hours "+*/RemainsMinutes+' min '+lastSec+' sec';
                        document.loginForm.Submit2.disabled=true;
                } else if(banTimerID != null) {
                    clearInterval(banTimerID);
                    document.loginForm.Submit2.disabled=false;
                }

            }
            function checkForm(form){
                if(banTimerID != null){
                       form.submit();
                       return true;
                } else form.Submit2.disabled=true;
                return false;
            }
            banTimerID = setInterval("updateBanTimer()", 1000);
            //-->
        </script>
<{/if}>
    </head>
    <body>
<{if !empty($refresh.head) or !empty($refresh.body)}>
    <{$refresh.body}>
<{else}>
        <form id="loginForm" name="loginForm" action="" method="post" onsubmit="<{if $bannedTime>0}>return checkForm(this);<{/if}>">
            <table width="300" border="0" cellspacing="5" cellpadding="5" class="sheet" style="border:1px solid #CCCCCC; margin:auto; margin-top:200px;width:250px;" align="center">
                <tr>
                    <td colspan="2">
                        <br/>
                        &nbsp;&nbsp;&nbsp;<strong><{$smarty.const.WELCOME_TO_ADMIN}></strong>&nbsp;&nbsp;<a href="http://weblife.ua" style="text-decoration:none;">&reg;</a>
                        <br/>
                        &nbsp;&nbsp;&nbsp;<{$messages.top}><br/><br/>
                    </td>
                </tr>
                <tr>
                    <td width="70">
                        <{$smarty.const.USERS_LOGIN}>:
                    </td>
                    <td>
                        <input type="text" name="login" value="" size="36"/>
                    </td>
                </tr>
                <tr>
                    <td width="70">
                        <{$smarty.const.USERS_PASS}>:
                    </td>
                    <td>
                        <input name="pass" value="" type="password" size="36"/>
                    </td>
                </tr>
<{if $showCode}>
                <tr>
                    <td colspan="2" style="padding-top: 10px;" valign="middle" align="center">
                        <img alt="code" src="/interactive/cv_img.php?zone=admin" border="0" align="top"/>&nbsp;
                        Confirmation code: 
                        <input type="text" id="fConfirmationCode" name="fConfirmationCode" value="" maxlength="<{$smarty.const.IVALIDATOR_MAX_LENTH}>" size="10"/>
                    </td>
                </tr>
<{/if}>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <br/>
                        <input type="submit" name="Submit2" value="<{$smarty.const.BUTTON_ENTER}>" class="buttons"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <{$messages.bottom}><br/>
                    </td>
                </tr>
            </table>
        </form>
<{/if}>
    </body>
</html>