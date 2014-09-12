<?php

move_uploaded_file($_FILES['factura']['tmp_name'], "media/archivosJoomla/".$_FILES['factura']['name']);

$xmlFileData    = file_get_contents("media/archivosJoomla/".$_FILES['factura']['name']);

include('libraries/integradora/xmlparser.php');

$data = new xml2Array();

$datos = $data->manejaXML($xmlFileData);

var_dump($datos);