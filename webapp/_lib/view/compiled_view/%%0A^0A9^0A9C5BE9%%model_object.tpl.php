<?php /* Smarty version 2.6.26, created on 2011-09-15 18:53:35
         compiled from /Users/gina/Sites/callbax/extras/dev/makemodel/view/model_object.tpl */ ?>
class <?php echo $this->_tpl_vars['object_name']; ?>
 <?php echo '{'; ?>

<?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>
    /**
     * @var <?php echo $this->_tpl_vars['field']['PHPType']; ?>
<?php if ($this->_tpl_vars['field']['Comment']): ?> <?php echo $this->_tpl_vars['field']['Comment']; ?>
<?php endif; ?>

     */
    var $<?php echo $this->_tpl_vars['field']['Field']; ?>
<?php if ($this->_tpl_vars['field']['PHPType'] == 'bool'): ?> = false<?php endif; ?>;
<?php endforeach; endif; unset($_from); ?>
    public function __construct($row = false) <?php echo '{'; ?>

        if ($row) <?php echo '{'; ?>

<?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>
<?php if ($this->_tpl_vars['field']['PHPType'] == 'bool'): ?>
            $this-><?php echo $this->_tpl_vars['field']['Field']; ?>
 = PDODAO::convertDBToBool($row['<?php echo $this->_tpl_vars['field']['Field']; ?>
']);
<?php else: ?>
            $this-><?php echo $this->_tpl_vars['field']['Field']; ?>
 = $row['<?php echo $this->_tpl_vars['field']['Field']; ?>
'];
<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
        <?php echo '}'; ?>

    <?php echo '}'; ?>

<?php echo '}'; ?>

