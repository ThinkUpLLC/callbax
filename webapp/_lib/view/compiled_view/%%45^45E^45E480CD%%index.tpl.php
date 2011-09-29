<?php /* Smarty version 2.6.26, created on 2011-09-29 14:24:32
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'relative_datetime', 'index.tpl', 71, false),array('modifier', 'urlencode', 'index.tpl', 78, false),)), $this); ?>
<html>
<head>
<title>ThinkUp Analytics</title>
<style type="text/css">
<?php echo '
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
'; ?>

</style>

</head>
<body>
<center>
<div id="canvas">

<div class="statcontainer"><?php echo $this->_tpl_vars['total_installations']; ?>
</div> <div class="statexplainer">admins used ThinkUp in the last <?php echo ((is_array($_tmp=$this->_tpl_vars['first_seen_installation_date'])) ? $this->_run_mod_handler('relative_datetime', true, $_tmp) : smarty_modifier_relative_datetime($_tmp)); ?>
</div>
<br style="clear:both">
<br><br>

<?php if ($this->_tpl_vars['service_stats']): ?>
<div style="float:left">
Service users by network<br/>
<img src="http://chart.apis.google.com/chart?cht=p&chd=t:<?php $_from = $this->_tpl_vars['service_stats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['stat']):
        $this->_foreach['sloop']['iteration']++;
?><?php echo $this->_tpl_vars['stat']['percentage']; ?>
<?php if (! ($this->_foreach['sloop']['iteration'] == $this->_foreach['sloop']['total'])): ?>,<?php endif; ?><?php endforeach; endif; unset($_from); ?>&chs=400x200&chl=<?php $_from = $this->_tpl_vars['service_stats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['stat']):
        $this->_foreach['sloop']['iteration']++;
?><?php echo ((is_array($_tmp=$this->_tpl_vars['stat']['service'])) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
+(<?php echo $this->_tpl_vars['stat']['count']; ?>
)<?php if (! ($this->_foreach['sloop']['iteration'] == $this->_foreach['sloop']['total'])): ?>|<?php endif; ?><?php endforeach; endif; unset($_from); ?>&chco=6184B5,E6E6E6"/>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['version_stats']): ?>
Installations by version
<img src="http://chart.apis.google.com/chart?cht=p&chd=t:<?php $_from = $this->_tpl_vars['version_stats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['stat']):
        $this->_foreach['sloop']['iteration']++;
?><?php echo $this->_tpl_vars['stat']['percentage']; ?>
<?php if (! ($this->_foreach['sloop']['iteration'] == $this->_foreach['sloop']['total'])): ?>,<?php endif; ?><?php endforeach; endif; unset($_from); ?>&chs=400x200&chl=<?php $_from = $this->_tpl_vars['version_stats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['sloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['sloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['skey'] => $this->_tpl_vars['stat']):
        $this->_foreach['sloop']['iteration']++;
?>v<?php echo ((is_array($_tmp=$this->_tpl_vars['stat']['version'])) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
+(<?php echo $this->_tpl_vars['stat']['count']; ?>
)<?php if (! ($this->_foreach['sloop']['iteration'] == $this->_foreach['sloop']['total'])): ?>|<?php endif; ?><?php endforeach; endif; unset($_from); ?>&chco=6184B5,E6E6E6"/>
<?php endif; ?>
<br style="clear:both">
<br><br>


<div>
<?php if ($this->_tpl_vars['next_page']): ?>
<a href="?p=<?php echo $this->_tpl_vars['next_page']; ?>
">&larr;Older</a>
<?php endif; ?>
&nbsp;
<?php if ($this->_tpl_vars['prev_page']): ?>
<a href="?p=<?php echo $this->_tpl_vars['prev_page']; ?>
">Newer&rarr;</a>
<?php endif; ?>
</div>
<table class="gridtable">
    <tr>
        <th>Location</th>
        <th>Version</th>
        <th>Last seen</th>
        <th>Service user(s)</th>
    </tr>
<?php $this->assign('prev_url', ''); ?>
<?php $_from = $this->_tpl_vars['installations']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['iloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['iloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['ikey'] => $this->_tpl_vars['installation']):
        $this->_foreach['iloop']['iteration']++;
?>
    <?php if ($this->_tpl_vars['installation']['url'] != $this->_tpl_vars['prev_url']): ?>
    <?php if ($this->_tpl_vars['prev_url'] != ''): ?>
    </td></tr>
    <?php endif; ?>
    <tr>
        <td><?php if ($this->_tpl_vars['installation']['url'] != $this->_tpl_vars['prev_url']): ?><?php echo $this->_tpl_vars['installation']['url']; ?>
<?php else: ?>&nbsp;<?php endif; ?></td>
        <td><?php echo $this->_tpl_vars['installation']['version']; ?>
</td>
        <td><?php echo ((is_array($_tmp=$this->_tpl_vars['installation']['last_seen'])) ? $this->_run_mod_handler('relative_datetime', true, $_tmp) : smarty_modifier_relative_datetime($_tmp)); ?>
 ago</td>
        <td><?php if ($this->_tpl_vars['installation']['service'] == 'Twitter'): ?><a href="http://twitter.com/<?php echo $this->_tpl_vars['installation']['username']; ?>
">@<?php echo $this->_tpl_vars['installation']['username']; ?>
</a><?php else: ?><?php echo $this->_tpl_vars['installation']['username']; ?>
 | <?php echo $this->_tpl_vars['installation']['service']; ?>
<?php endif; ?>
    <?php else: ?>
        <br><?php if ($this->_tpl_vars['installation']['service'] == 'Twitter'): ?><a href="http://twitter.com/<?php echo $this->_tpl_vars['installation']['username']; ?>
">@<?php echo $this->_tpl_vars['installation']['username']; ?>
</a><?php else: ?><?php echo $this->_tpl_vars['installation']['username']; ?>
 | <?php echo $this->_tpl_vars['installation']['service']; ?>
<?php endif; ?>
    <?php endif; ?>
    <?php $this->assign('prev_url', $this->_tpl_vars['installation']['url']); ?>
<?php endforeach; endif; unset($_from); ?>
</td></tr>
</table>
</div>
</center>
</body>
</html>