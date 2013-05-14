<form action="" method="post">
    <div>
        <label for="login_username">邮箱/用户名</label>
        <input type="text" name="LoginForm[username]" id='login_username' value="{$model->username}">
    </div>
    <div>
        <label for="login_password">密码</label>
        <input type="password" name="LoginForm[password]" id='login_password' value="{$model->password}">
    </div>
    <div>
        <label for="remember_me">记住密码？</label>
        <input type="checkbox" name="LoginForm[rememberMe]" {if $model->rememberMe }checked {/if} id='remember_me' value="1">
    </div>
    <div>
        <input type="submit" value="登录">
    </div>
</form>