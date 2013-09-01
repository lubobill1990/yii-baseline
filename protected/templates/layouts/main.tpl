<!DOCTYPE html>
<html>
<head>
    <title>{$page_title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {block name=css}
    {/block}
    <script type="text/javascript" src='/javascripts/require.2.1.5.js'></script>
    <script type="text/javascript" src='/javascripts/main.js'></script>
    <link rel="stylesheet" href="/stylesheets/screen.css">
    <!--[if IE 7]>
    <link rel="stylesheet" href="/stylesheets/font-awesome-ie7.min.css">
    <![endif]-->
    <script type="text/javascript" src="/javascripts/custom.modernizr.js"></script>
</head>
<body>
{block name=header}
{include file='layouts/header.tpl'}
{/block}
<div id="wrapper">
    <div class="row">
        <div id="content-left" class="columns large-9">
        {block name=left}{/block}
        </div>
        <div id="content-right" class="columns large-3">
        {block name=right}{/block}
        </div>
    </div>
</div>
{block name=footer}
{include file='layouts/footer.tpl'}
{/block}
{block name=js}{/block}
</body>
</html>
