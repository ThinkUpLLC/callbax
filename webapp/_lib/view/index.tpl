{*
Render a page of installations.

Parameters:
$installations (required) Array of Installation objects
$next_page (required) Either an integer or boolean false
$prev_page (required) Either an integer or boolean false
$first_seen_installation_date (required) The date of the oldest installation record, or, when recording started
$service_stats (optional) Array of counts and percentages of service users by network
$version_stats (optional) Array of counts and percentages of installations by version
*}
<html>
<head>
<title>ThinkUp Analytics</title>
<style type="text/css">
{literal}
body {
    font-family: verdana,arial,sans-serif;
}
table.gridtable {
    width:100%;
    font-size:11px;
    color:#333333;
    border-width: 1px;
    border-color: #666666;
    border-collapse: collapse;
}
table.gridtable th {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #dedede;
}
table.gridtable td {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #ffffff;
}
#canvas {
    text-align:left;
    width:800px;
}
.statcontainer {
    background-color:black;
    font-size:350%;
    font-weight:bold;
    color:white;
    text-align:center;
    float:left;
    -moz-border-radius: 7px;
    border-radius: 7px;
    padding:5px;
    margin:0 5px 5px 0;
}
.statexplainer {
    font-size:13px;
    width:110px;
    float:left;
}
{/literal}
</style>

</head>
<body>
<center>
<div id="canvas">

<div class="statcontainer">{$total_installations}</div> <div class="statexplainer">admins used ThinkUp in the last {$first_seen_installation_date|relative_datetime}</div>
<br style="clear:both">
<br><br>

{if $service_stats}
<div style="float:left">
Service users by network<br/>
<img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$service_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$service_stats key=skey item=stat name=sloop}{$stat.service|urlencode}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=6184B5,E6E6E6"/>
</div>
{/if}
{if $version_stats}
Installations by version (v0.12+ only)
<img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$version_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$version_stats key=skey item=stat name=sloop}v{$stat.version|urlencode}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=6184B5,E6E6E6"/>
{/if}
<br style="clear:both">
<br><br>
{if $usercount_stats}
Service user count distribution<br />
<img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$usercount_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$usercount_stats key=skey item=stat name=sloop}{$stat.user_count}+user{if $stat.user_count > 1}s{/if}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=6184B5,E6E6E6"/>
{/if}
<br><br>


<table class="gridtable">
    <tr>
        <th>Location</th>
        <th>Version</th>
        <th>Last seen</th>
        <th>Service user(s)</th>
    </tr>
{assign var="prev_url" value=''}
{foreach from=$installations key=ikey item=installation name=iloop}
    {if $installation.url neq $prev_url}
    {if  $prev_url neq ''}
    </td></tr>
    {/if}
    <tr>
        <td>{if $installation.url neq $prev_url}{$installation.url}{else}&nbsp;{/if}</td>
        <td>{$installation.version}</td>
        <td>{$installation.last_seen|relative_datetime} ago</td>
        <td>{if $installation.service eq "Twitter"}<a href="http://twitter.com/{$installation.username}">@{$installation.username}</a>{else}{$installation.username} | {$installation.service}{/if}
    {else}
        <br>{if $installation.service eq "Twitter"}<a href="http://twitter.com/{$installation.username}">@{$installation.username}</a>{else}{$installation.username} | {$installation.service}{/if}
    {/if}
    {assign var="prev_url" value=$installation.url}
{/foreach}
</td></tr>
</table>
</div>
</center>
</body>
</html>