{block name=left}
<form action="" method="POST">
    <div class="row">
        <div class="columns large-2 small-3">
            <label for="resend_email" class="right inline">电子邮箱</label>
        </div>
        <div class="columns large-5 small-7">
            <input type="text" name="email" id='resend_email' value="{$email|default:''}">
            {if $errors['user']|default:false}
                <small class="error">{$errors['user']}</small>
            {/if}
        </div>
        <div class="large-5 columns hide-for-small"></div>
    </div>
    <div class="row">
        <div class="columns large-2 small-3">
            <label for="login_captcha_text" class="right inline">输入图片中单词</label>
        </div>
        <div class="columns large-2 small-3">
            <input type="text" id="login_captcha_text" name='captcha'>
            {if $errors['captcha']|default:false}
                <small class="error">{$errors['captcha']}</small>
            {/if}
        </div>
        <img class="captcha captcha-img columns large-3 small-4" src="/captcha" alt="" style="height:28px">

        <div class="columns large-5 hide-for-small">

        </div>
    </div>
    <div class="row">
        <div class="columns small-offset-3 small-4 large-offset-2 large-3">
            <input type="submit" value="确认" id='resend_submit' class="expand button"/>
        </div>
    </div>
</form>
{/block}