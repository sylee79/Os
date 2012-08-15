<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.echo.php
 * Type:     function
 * Name:     echo
 * Purpose:  echo the variable if it isset
 * -------------------------------------------------------------
 */
function smarty_function_echo($params, &$smarty)
{
    if(isset($smarty->parent->tpl_vars[$params['var']]))
    {
        return $smarty->parent->tpl_vars[$params['var']]->value;
    }
    return isset($params['default'])?$params['default']:"";
}
?>