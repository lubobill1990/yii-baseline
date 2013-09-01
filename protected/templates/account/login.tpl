{block name=css}
<link rel="stylesheet" href="/stylesheets/page/login.css">
{/block}
{block name=left}
<div class="bc" id="user-main">
    <h1>登录{$YiiApp->name}</h1>
    <article>
        <div id="user-cloud"></div>
        <form class="custom user-form" action="" method="post" id='login_form'>
            <input type="hidden" name="return_url" value="{$return_url}">

            <div class="row">
                <div class="columns large-2 small-3">
                    <label for="login_username" class="right inline">邮箱/用户名</label>
                </div>
                <div class="columns large-5 small-7">
                    <input type="text" name="LoginForm[username]" id='login_username' value="{$model->username}">
                    {if $errors['username']|default:false}
                        <small class="error">邮箱/用户名或密码错误，或者用户
                            <a href="/account/resend-activate-code" title="发送新的激活码到您的邮箱">未激活</a></small>
                    {/if}

                </div>
                <div class="large-5 columns hide-for-small">
                </div>

            </div>
            <div class="row">
                <div class="columns large-2 small-3">
                    <label for="login_password" class="right inline">密码</label>
                </div>
                <div class="columns large-5 small-7">
                    <input type="password" name="LoginForm[password]" id='login_password' value="{$model->password}">
                </div>
                <div class="columns"></div>
            </div>

            {if $show_captcha}
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
            {/if}
            <div class="row more-item">
                <div class="large-offset-2 large-3 columns small-offset-3 small-4">
                    <label for="remember_me">
                        <input type="checkbox" name="LoginForm[rememberMe]" {if $model->rememberMe } checked {/if}
                               id='remember_me' style="display: none;">
                        <span class="custom checkbox {if $model->rememberMe } checked {/if}"></span> 记住密码？
                    </label>
                </div>
                <div class="columns small-4 large-4 left">
                    <a href="/account/retrieve-password"
                       class="link-retrieve-password">忘记密码了>_<</a>
                </div>
            </div>
            <div class="row">
                <div class="columns small-offset-3 small-4 large-offset-2 large-3">
                    <input type="submit" value="登录" id="user-submit" class="expand button"/>
                </div>
            </div>
        </form>
    </article>
</div>
{/block}

{block name=js}
<script type="text/javascript">
    require(['jquery', 'foundation/foundation', 'foundation/foundation.forms'], function ($) {
        $('.input-wrapper input').focusin(function () {
            $(this).parent().addClass('input-wrapper-hover');
        }).focusout(function () {
                    $(this).parent().removeClass('input-wrapper-hover');
                })
        $(document).foundation();
    });
</script>
{/block}
