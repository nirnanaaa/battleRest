<?php
require '../../rest.php';
echo '<pre>
$restAPI = new REST();
/*
 *if you want to enable apc using try to use
 *$restAPI->apcCaching = true;
 *if you want to use memcached try:
 *$restAPI->memCached = true;
 *otherwise it will be disabled
 */
$restAPI->realm = \'blackrock\';
$restAPI->character = \'mosny IMAGE\';
echo $restAPI->character;
</pre>
';
$cla = new REST();
$cla->realm = 'blackrock';
$cla->character = 'mosny IMAGE';
echo '<pre>';
print_r('<img src="'.$cla->character.'" />');
echo '</pre>';