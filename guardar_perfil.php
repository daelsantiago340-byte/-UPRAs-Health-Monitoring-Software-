<?php

include "conexion.php";

$nombre = $_POST['nombre'];
$edad = $_POST['edad'];
$peso = $_POST['peso'];
$sexo = $_POST['sexo'];
$estatura = $_POST['estatura'];
$enfermedades = $_POST['enfermedades'];

$sql="INSERT INTO perfil
(nombre,edad,peso,sexo,estatura,
enfermedades_prediagnosticadas)

VALUES
('$nombre','$edad','$peso',
'$sexo','$estatura','$enfermedades')";

$conn->query($sql);

header("Location:index.php");
?>