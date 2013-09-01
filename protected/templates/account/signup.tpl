{block name=css}
<link rel="stylesheet" href="/stylesheets/special/user/user.css">
{/block}


{block name=left}
<div class="bc" id="user-main">
    <h2>欢迎加入{$YiiApp->name}</h2>

    <div id="user-cloud"></div>
    <form class="user-form" action="" method="post" id='signup_form'>
        <div class="row">
            <div class="columns large-2 small-3">
                <label for="user_email" class="right inline">电子邮箱</label>
            </div>
            <div class="columns large-5 small-7">
                <input type="text" name='User[email]' id='user_email' value="{$user->email}">
                {if $errors['email']|default:false}
                    <small class="error">{$errors['email']}</small>
                {/if}
            </div>
            <div class="large-5 columns hide-for-small">
            </div>
        </div>
        <div class="row">
            <div class="columns large-2 small-3">
                <label for="user_name" class="right inline">用户名</label>
            </div>
            <div class="columns large-5 small-7">
                <input type="text" name="User[username]" id='user_name' value="{$user->username}">
                {if $errors['username']|default:false}
                    <small class="error">{$errors['username']}</small>
                {/if}
            </div>
            <div class="large-5 columns hide-for-small">
            </div>
        </div>
        <div class="row">
            <div class="columns large-2 small-3">
                <label for="user_password" class="right inline">密码</label>
            </div>
            <div class="columns large-5 small-7">
                <input type="password" name='User[password]' id='user_password' value="{$password}">
                {if $errors['password']|default:false}
                    <small class="error">{$errors['password']}</small>
                {/if}
            </div>
            <div class="large-5 columns hide-for-small">
            </div>
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
                <input type="submit" value="登录" id="user-submit" class="expand button"/>
            </div>
        </div>
    </form>
</div>
{/block}


{block name=js}
<script type="text/javascript">
    require(['jquery'], function ($) {
        $('.input-wrapper input').focusin(function () {
            $(this).parent().addClass('input-wrapper-hover');
        }).focusout(function () {
                    $(this).parent().removeClass('input-wrapper-hover');
                })
    });
</script>
{/block}