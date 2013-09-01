{block name=left}
<form action="" method="post">
    <div class="row">
        <div class="columns large-2 small-3">
            <label for="retrieve_email" class="right inline">电子邮箱</label>
        </div>
        <div class="columns large-5 small-7">
            <input type="text" name="email" id='retrieve_email' value="{$email}">
            {if $errors['user']|default:false}
                <small class="error">请输入正确的电子邮箱</small>
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
                <small class="error">验证码错误</small>
            {/if}
        </div>
        <img class="captcha captcha-img columns large-3 small-4" src="/captcha" alt="" style="height:28px">

        <div class="columns large-5 hide-for-small">

        </div>
    </div>

    <div class="row">
        <div class="columns small-offset-3 small-4 large-offset-2 large-3">
            <input type="submit" value="确认" id='retrieve_submit' class="expand button"/>
        </div>
    </div>

</form>
{/block}