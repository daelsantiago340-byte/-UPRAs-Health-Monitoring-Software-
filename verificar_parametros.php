<?php

function verificarParametro(
$valor,
$min,
$max){

if($valor < $min)
return "BAJO";

if($valor > $max)
return "ALTO";

return "NORMAL";
}
?>