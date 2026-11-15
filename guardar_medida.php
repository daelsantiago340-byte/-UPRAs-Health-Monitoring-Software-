<?php

include "conexion.php";
include "verificar_parametros.php";

$nombre = $_POST['nombre'];

/* ==========================
   VALIDAR USUARIO
========================== */

$resultadoUsuario =
$conn->query(
"SELECT * FROM perfil
WHERE nombre='$nombre'"
);

if($resultadoUsuario->num_rows == 0){

    echo "

    <script>

    alert('Usuario no encontrado');

    window.location.href='index.php';

    </script>

    ";

    exit();
}

$usuario = $resultadoUsuario->fetch_assoc();

$id = $usuario['id_perfil'];

$sexo = $usuario['sexo'];

/* ==========================
   DATOS DEL FORMULARIO
========================== */

$sistolica = $_POST['sistolica'];
$diastolica = $_POST['diastolica'];

$temp = $_POST['temperatura'];

$spo2 = $_POST['spo2'];

$latidos = $_POST['latidos'];

$radiacion = $_POST['radiacion'];

$estado_animo = $_POST['estado_animo'];

$horas_dormidas = $_POST['horas_dormidas'];

$calidad_sueno = $_POST['calidad_sueno'];

/* ==========================
   NIVEL DE RIESGO
========================== */

$riesgo = "NORMAL";

if(
    $spo2 < 92 ||
    $temp > 38 ||
    $latidos > 120 ||
    $sistolica > 140
){
    $riesgo = "CRITICO";
}
elseif(
    $spo2 < 95 ||
    $temp > 37.5 ||
    $latidos > 100 ||
    $sistolica > 130
){
    $riesgo = "RIESGO";
}

/* ==========================
   GUARDAR MEDIDAS
========================== */

$sql = "

INSERT INTO medidas(

id_perfil,
presion_sistolica,
presion_diastolica,
temperatura,
spo2,
latidos_minuto

)

VALUES(

$id,
$sistolica,
$diastolica,
$temp,
$spo2,
$latidos

)

";

$conn->query($sql);

/* ==========================
   GUARDAR RADIACION
========================== */

$conn->query(
"INSERT INTO radiacion
(id_perfil,nivel_radiacion)

VALUES
(
$id,
'$radiacion'
)"
);

/* ==========================
   GUARDAR SALUD MENTAL
========================== */

$conn->query(
"INSERT INTO salud_mental
(id_perfil,estado_animo)

VALUES
(
$id,
'$estado_animo'
)"
);

/* ==========================
   GUARDAR SUENO
========================== */

$conn->query(
"INSERT INTO sueno(

id_perfil,
horas_dormidas,
calidad_sueno

)

VALUES(

$id,
'$horas_dormidas',
'$calidad_sueno'

)"
);

/* ==========================
   PARAMETROS NORMALES
========================== */

$normal =
$conn->query(
"SELECT *
FROM parametros_normales
WHERE sexo='$sexo'"
)->fetch_assoc();
if($radiacion > $normal['radiacion_max']){

    echo "

    <div class='card'>

    <h2 style='color:red'>
    ☢ Radiación Elevada
    </h2>

    <p>
    El nivel de radiación supera los parámetros normales.
    </p>

    </div>

    ";
}if($horas_dormidas < $normal['sueno_min']){

    echo "

    <div class='card'>

    <h2 style='color:orange'>
    😴 Déficit de Sueño
    </h2>

    <p>
    Las horas de sueño se encuentran por debajo del rango saludable.
    </p>

    </div>

    ";
}if($calidad_sueno < $normal['calidad_sueno_min']){

    echo "

    <div class='card'>

    <h2 style='color:orange'>
    🛌 Calidad de Sueño Baja
    </h2>

    <p>
    La calidad del sueño registrada es inferior a la recomendada.
    </p>

    </div>

    ";
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Resultado del Análisis</title>

<style>

body{
    background:#071829;
    color:white;
    font-family:Arial;
    text-align:center;
}

.card{

    background:#102840;

    width:80%;

    margin:auto;

    margin-top:25px;

    padding:20px;

    border-radius:15px;

    box-shadow:
    0 0 15px rgba(0,255,255,.3);
}

.boton{

    background:#00d4ff;

    color:black;

    padding:15px 25px;

    border:none;

    border-radius:10px;

    text-decoration:none;

    font-weight:bold;
}

</style>

</head>

<body>

<h1>🚀 NASA HEALTH MONITOR</h1>

<div class="card">

<h2>Estado General</h2>

<?php

if($riesgo=="CRITICO"){

    echo "
    <h1 style='color:red'>
    🔴 CRÍTICO
    </h1>
    ";
}
elseif($riesgo=="RIESGO"){

    echo "
    <h1 style='color:orange'>
    🟠 RIESGO
    </h1>
    ";
}
else{

    echo "
    <h1 style='color:lime'>
    🟢 NORMAL
    </h1>
    ";
}

?>

</div>

<?php
$alerta =
$conn->query(
"SELECT *
FROM alertas_medicas
WHERE parametro='Radiacion'
LIMIT 1"
)->fetch_assoc();
echo $alerta['alerta'];
echo $alerta['posibles_causas'];
echo $alerta['consecuencias'];
echo $alerta['recomendaciones'];

$estado_spo2 =
verificarParametro(
$spo2,
$normal['spo2_min'],
$normal['spo2_max']
);

if($estado_spo2!="NORMAL"){

$alerta =
$conn->query(
"SELECT *
FROM alertas_medicas
WHERE parametro='SPO2'
LIMIT 1"
)->fetch_assoc();

if($alerta){

?>

<div class="card">

<h2 style="color:red;">
<?php echo $alerta['alerta']; ?>
</h2>

<p>

<b>Posibles causas:</b><br>

<?php echo $alerta['posibles_causas']; ?>

</p>

<p>

<b>Consecuencias:</b><br>

<?php echo $alerta['consecuencias']; ?>

</p>

<p>

<b>¿Qué hacer?</b><br>

<?php echo $alerta['recomendaciones']; ?>

</p>

</div>

<?php

}

}

?>

<div class="card">

<h2>Resumen de la Medición</h2>

<p><b>Presión:</b> <?php echo $sistolica.'/'.$diastolica; ?></p>

<p><b>Temperatura:</b> <?php echo $temp; ?> °C</p>

<p><b>SPO2:</b> <?php echo $spo2; ?> %</p>

<p><b>Latidos:</b> <?php echo $latidos; ?> BPM</p>

<p><b>Radiación:</b> <?php echo $radiacion; ?> mSv</p>

<p><b>Estado de ánimo:</b> <?php echo $estado_animo; ?></p>

<p><b>Horas dormidas:</b> <?php echo $horas_dormidas; ?></p>

<p><b>Calidad de sueño:</b> <?php echo $calidad_sueno; ?>/10</p>

</div>

<br><br>

<a
class="boton"
href="estadisticas.php?nombre=<?php echo $nombre; ?>"
>
📊 Ver Estadísticas
</a>
<a
class="boton"
href="index.php?nombre=<?php echo $nombre; ?>"
>
Monitors
</a>
</body>
</html>