<style type="text/css">
    .subject_score_div {
        float: left;
        text-align: right;
        margin: 0 20px 0 10px;
        padding: 3px 5px;
        width: 80px;
    }

    .subject_score {
        margin-top: 4px;
    }
    .subject_score {
        display: block;
        width: 78px;
        height: 16px;
        text-align: left;
        float: left;
        background: url("http://0.web.qstatic.com/webqqpic/module/appmarket/images/sprite.png?t=20111011001") no-repeat -280px -2px;
    }

    .score {
        width: 100%;
        height: 100%;
        background: url("http://0.web.qstatic.com/webqqpic/module/appmarket/images/sprite.png") no-repeat -280px -20px;
    }

    .score_vote_count {
        margin: 8px 0 0 0;
        color: #969696;
    }
</style>
<script type="text/javascript" src='/js/wenji.score.js'></script>
<div class="subject_score_div" subject_type='{$subject_type}' subject_id='{$subject_id}' >
    <div class="subject_score" title="{$score}分" style="position:relative;">
        <div class="score" style="width: {math equation="score*20" score=$score format="%.2f"}%"></div>
    </div>
    <div class="score_vote_count" title="人气：{if $score_vote_count eq 0}0{else}{$score_vote_count}{/if}">{if $score_vote_count eq 0}0{else}{$score_vote_count}{/if}</div>
</div>