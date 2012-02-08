<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ThinkUp Analytics</title>
    <meta name="description" content="">
    <meta name="author" content="ThinkUp LLC">

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

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <style>

    </style>
    <link href="./assets/css/bootstrap.css" rel="stylesheet">

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="./assets/img/favicon.ico">
    <link rel="apple-touch-icon" href="./assets/img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="./assets/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="./assets/img/apple-touch-icon-114x114.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Project name</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

		<div class="statcontainer">{$total_installations}</div> <div class="statexplainer">admins used ThinkUp in the last {$first_seen_installation_date|relative_datetime}</div>



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
{if $usercount_stats}
		Service user count distribution<br />
		<img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$usercount_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$usercount_stats key=skey item=stat name=sloop}{$stat.user_count}+user{if $stat.user_count > 1}s{/if}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=6184B5,E6E6E6"/>
{/if}



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

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="./assets/js/bootstrap.js"></script>

  </body>
</html>
