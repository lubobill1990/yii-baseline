<div>
    <p><span>{$user->email}</span>，您好，</p>

    <p>您在 <span>{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}</span> 请求重置密码，请点击以下链接继续：</p>

    <p><a href="{$YiiApp->getBaseUrl(true)}/account/reset_password?user_id={$user->id}&key={$key}">{$YiiApp->getBaseUrl(true)}/account/reset_password?user_id={$user->id}&key={$key}</a></p>

    <p>如果上述链接不能点击，请复制到浏览器地址栏访问</p>

</div>