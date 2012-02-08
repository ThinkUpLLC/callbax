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

    <div class="navbar">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="http://thinkupapp.com/callbax/">Callbax: <strong>Think</strong>Up Stats</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="http://thinkupapp.com/">ThinkUpApp.com</a></li>
              <li><a href="http://reports.expertlabs.org/fsmi/">FSMI</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

    <div class="span2 offset1 alert alert-error">
        <h1>{$total_installations} admins<h1>
        <h4>used ThinkUp in the last<h4>
        <h1>{$first_seen_installation_date|relative_datetime}</h1>
                
    </div>

    <div class="span5 offset1">
          <div class="accordion" id="accordion2">
          
{if $service_stats}
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle btn" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                  By Network
                </a>
              </div>
              <div id="collapseOne" class="accordion-body collapse in">
                <div class="accordion-inner">
                  <img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$service_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$service_stats key=skey item=stat name=sloop}{$stat.service|urlencode}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=3E5D9A,3C8ECC"/>
                </div>
              </div>
            </div>
{/if}

{if $version_stats}
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle btn" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                  By Version
                </a>
              </div>
              <div id="collapseTwo" class="accordion-body collapse">
                <div class="accordion-inner">
                  <img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$version_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$version_stats key=skey item=stat name=sloop}v{$stat.version|urlencode}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=3E5D9A,3C8ECC"/>
                </div>
              </div>
            </div>
{/if}

{if $usercount_stats}
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle btn" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
                  # of Users
                </a>
              </div>
              <div id="collapseThree" class="accordion-body collapse">
                <div class="accordion-inner">
                  <img src="http://chart.apis.google.com/chart?cht=p&chd=t:{foreach from=$usercount_stats key=skey item=stat name=sloop}{$stat.percentage}{if !$smarty.foreach.sloop.last},{/if}{/foreach}&chs=400x200&chl={foreach from=$usercount_stats key=skey item=stat name=sloop}{$stat.user_count}+user{if $stat.user_count > 1}s{/if}+{'('|urlencode}{$stat.percentage}{'%)'|urlencode}{if !$smarty.foreach.sloop.last}|{/if}{/foreach}&chco=3E5D9A,3C8ECC"/>
                </div>
              </div>
            </div>
{/if}
          </div>
          
    </div>


    <div class="span12">
    
<table class="table-striped table-bordered table-condensed">
    <thead>
        <th>Location</th>
        <th>Last seen</th>
        <th>Ver</th>
        <th>Service</th>
        <th>User</th>
    </thead>
{assign var="prev_url" value=''}
{foreach from=$installations key=ikey item=installation name=iloop}
    {if $installation.url neq $prev_url}
    <tr style="border-top : 1px solid silver;">
        <td>{$installation.url}</td>
        <td><h6>{$installation.last_seen|relative_datetime} ago</h6></td>
        <td><h6>{$installation.version}</h6></td>
        <td><h6>{$installation.service}</h6></td>
        <td>{if $installation.service eq "Twitter"}<a href="http://twitter.com/{$installation.username}">@{$installation.username}</a>{else}{$installation.username}{/if}</td>
    </tr>
    {else}
    <tr>
        <td></td><td></td><td></td><td></td><td>{if $installation.service eq "Twitter"}<a href="http://twitter.com/{$installation.username}">@{$installation.username}</a>{else}{$installation.username} | {$installation.service}{/if}</td>
    </tr>
    {/if}
    {assign var="prev_url" value=$installation.url}
{/foreach}
</table>

    </div>

    </div> <!-- /container -->
    
<div class="footer span 12">&nbsp;</div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="./assets/js/bootstrap.js"></script>

  </body>
</html>