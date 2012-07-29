<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.init.php
 * Type:     function
 * Name:     init
 * Purpose:  echo the name and initialize the value if default value found
 * -------------------------------------------------------------
 */
function smarty_function_init($params, &$smarty)
{
    if(isset($smarty->parent->tpl_vars[$params['name']]))
    {
        return "name=\"".$params['name']."\" value=\"".$smarty->parent->tpl_vars[$params['name']]->value."\"";
    }

    if(isset($params['default']))
    	return "name=\"".$params['name']."\" value=\"".$params['default']."\"";
    else
    	return "name=\"".$params['name']."\" value=\"\"";

}
?>