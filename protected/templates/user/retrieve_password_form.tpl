<form action="" method="post">
    <div>
        <label for="retrieve_email">电子邮箱：</label>
        <input type="text" name="email" id='retrieve_email' value="{$email}">
        <input type="submit" value="确认" id='retrieve_submit'>
    </div>
    <div class="message">
        <ul>
        {foreach $messages as $message}
            <li>{$message}</li>
        {/foreach}
        </ul>
    </div>
</form>