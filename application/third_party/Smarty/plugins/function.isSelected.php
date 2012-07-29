<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.isSelected.php
 * Type:     function
 * Name:     echo
 * Purpose:  echo the variable if it isset
 * -------------------------------------------------------------
 */
function smarty_function_isSelected($params, &$smarty)
{
    if(isset($smarty->parent->tpl_vars[$params['selVar']])
    	&& $smarty->parent->tpl_vars[$params['selVar']]->value == $params['val'])
    {

        return 'selected';
    }
    return "";
}
?>