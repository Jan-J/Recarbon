<?php

require 'vendor/autoload.php';

use Recarbon\Carbon;
use Recarbon\Recarbon;

$c = Carbon::createFromDateTime('2012-03-20 12:37:43');
echo $c->toFullDateHuman();

echo "<br />";

$c = Recarbon::createFromDateTimeT('2012-03-20T12:37:43');
echo $c->toDateTimeTString();