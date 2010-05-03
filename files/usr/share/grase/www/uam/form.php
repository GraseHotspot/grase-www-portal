<?php
header("Content-Type: text/javascript;");
?>
var o = document.getElementById('logonForm');
if (o != null) {

o.innerHTML='<?php

$template=str_replace("'","\'",implode(file('json_html.tmpl',FILE_IGNORE_NEW_LINES)));
echo $template;

?>'
}
setTimeout('chilliController.refresh()', 0);

