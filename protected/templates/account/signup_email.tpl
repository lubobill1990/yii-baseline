<div class="title">注册成功</div>
<div class="content">
    <p><span>{$user->username}</span>，你好，</p>

    <p>欢迎您的加入，接下来您还需要激活您的账户</p>

    <p><a href="{$YiiApp->getBaseUrl(true)}/account/activate?user_id={$user->id}&key={$activate_code}">{$YiiApp->getBaseUrl(true)}/account/activate?user_id={$user->id}&key={$activate_code}</a>
    </p>
    <p>如果上述链接不能点击，请复制到浏览器地址栏访问</p>
</div>