<?php /* Smarty version Smarty-3.1.6, created on 2012-06-12 18:22:59
         compiled from "application/views/enduser/main.html" */ ?>
<?php /*%%SmartyHeaderCode:16129829854fc347c5b96857-24645297%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12f29c18f7326a00bd44036b72eb648ae707e6e6' => 
    array (
      0 => 'application/views/enduser/main.html',
      1 => 1339496570,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16129829854fc347c5b96857-24645297',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.6',
  'unifunc' => 'content_4fc347c5cb138',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4fc347c5cb138')) {function content_4fc347c5cb138($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php echo $_smarty_tpl->getSubTemplate ('enduser/header.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


<body>
<div id="container">
    <?php echo $_smarty_tpl->getSubTemplate ('enduser/top.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


    <?php echo $_smarty_tpl->getSubTemplate ('enduser/new_arrival.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


    <?php echo $_smarty_tpl->getSubTemplate ('enduser/bottom.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>
<!-- end #container -->
</body>
</html>

<?php }} ?>