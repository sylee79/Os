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
function smarty_function_datatable($params, &$smarty)
{
	$headers = explode(",", $params["headers"]);
	$keys = explode(",", $params["keys"]);
	$tableHTMLId = $params["table_id"];
	$rows = $params["rows"];
	$thHTML = "";
	foreach ($headers as $header) {
		if ($header == "EMPTY") {
			$thHTML .= "<th>&nbsp;</th>";
		} else {
			$thHTML .= "<th>$header</th>";
		}
	}
	$rowHTML = "";
	foreach ($rows as $row) {
		$rowHTML .= "<tr>";
		foreach ($keys as $key) {
			if ($key == "EMPTY") {
				$rowHTML .= "<td>&nbsp;</td>";
			} else {
				$rowHTML .= "<td>" . $row[$key] . "</td>";
			}
		}
		$rowHTML .= "</tr>";
	}
	$html = <<<HTML
<table cellpadding="0" cellspacing="0" border="0" class="display default_datatable" id="$tableHTMLId">
	<thead><tr>$thHTML</tr></thead>
	<tbody>$rowHTML</tbody>
	<tfoot><tr>$thHTML</tr></tfoot>
</table>
HTML;
	return $html;
}
?>