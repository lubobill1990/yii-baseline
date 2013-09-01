<div>
    <p><span>{$user->username}</span>，您好，</p>

    <p>您在 <span>{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</span> 修改了密码</p>

    <p>如果不是您本人操作，点击以下链接冻结账户并尽快与管理员联系</p>

    <p><a href="{$YiiApp->getBaseUrl(true)}/account/block?user_id={$user->id}&key={$key}">{$YiiApp->getBaseUrl(true)}/account/block?user_id={$user->id}&key={$key}</a></p>
</div>