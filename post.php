<?php
$xml = simplexml_load_file($_FILES['factura']['tmp_name'], 'archivos');

//var_dump($xml,$xml['Moneda']);