<form action="" method="POST">
    <input type="hidden" name="user_id" value="{$key->user_id}">
    <input type="hidden" name="key" value="{$key->key}">
    <div><label for="password_reset">重置密码：</label><input type="password" name="password" id='password_reset'></div>
    <input type="submit" value="确认">
</form>