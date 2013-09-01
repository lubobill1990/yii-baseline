{block name=left}
<span>{$user->username}</span> 您好，
<p>{if $block_from|default:"secure" == 'self'}由于您自己的操作{else}我们检测到您的账户存在安全隐患{/if}，您的账户已被暂时限制使用。
</p>
<p>
    账户一旦被限制，已使用您账户登录的用户将立刻不能继续使用您的账户操作
</p>
<p>
    如果您确保账户处于安全状态，可以点击以下链接恢复账户
    <a href="{$YiiApp->getBaseUrl(true)}/account/unblock?user_id={$user->id}&key={$key}">
        {$YiiApp->getBaseUrl(true)}/account/unblock?user_id={$user->id}&key={$key}</a>
</p>
{/block}