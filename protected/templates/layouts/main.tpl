<!DOCTYPE html>
<html>
<head>
    <title>{$page_title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script type="text/javascript" src='/javascripts/require.2.1.5.js'></script>
    <script type="text/javascript" src='/javascripts/main.js'></script>
{if $script_tpl|default:''}
    {$script_tpl}
{/if}
    <link rel="stylesheet" href="/stylesheets/screen.css">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/stylesheets/font-awesome-ie7.min.css">
    <![endif]-->
</head>
<body>
{include file='layouts/header.tpl'}
<div id="wrapper">
    <div class="container">
    {if $content_tpl|default:''}
        {$content_tpl}
    {/if}
    </div>
</div>
{include file='layouts/footer.tpl'}
<div class="testArea"></div>

</body>
</html>
