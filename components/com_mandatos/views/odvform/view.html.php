<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdvform extends JViewLegacy {
	
	function display($tpl = null){


            $this->clientes     = $this->get('clientes');
            $this->proyectos    = $this->get('proyectos');
            $this->estados      = $this->get('estados');
            $this->solicitud    = $this->get('datosSolicitud');

        if (count($errors = $this->get('Errors'))) {
	        JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

        $script=<<<EOD


        jQuery(document).ready(function(){
				jQuery('button').click(function(){
				var serializado     =   jQuery('table#odv').find('input,select').serialize();
                var produc=new Array(3)
                produc[0] = "Abierto"
                produc[1] = "P 1"
                produc[2] = "P 2"
                produc[3] = "P 3"
                produc[4] = "P 4"
                produc[5] = "P 5"
                produc[6] = "P 6"
                produc[7] = "P 7"
                produc[8] = "P 8"



                var tabla=document.getElementById("odv");

				 var tr=document.createElement("tr");

				 for (var j = 0; j < 9; j++) {
                              // Crea un elemento <td> y un nodo de texto, haz que el nodo de
                              // texto sea el contenido de <td>, ubica el elemento <td> al final
                              // de la hilera de la tabla
                              var celda     =   document.createElement("td");
                              var random    =   Math.floor((Math.random() * 50) + 1);
                              switch (j) {
                                  case 0:
                                        var select          =   document.createElement('select');
                                        var posicion        =   document.getElementById('productos').options.selectedIndex;
                                        var eliminar        =   document.getElementById('productos').options[posicion].text;
                                        select.name         =   'productos'+random;

                                        for (i=0; i<9; i++){
                                            opt             = document.createElement('option');
                                            opt.value       = i;
                                            if (eliminar != produc[i]){
                                                opt.innerHTML   = produc[i];
                                                select.appendChild(opt);
                                            }


                                        }

                                        celda.appendChild(select);
                                    break;
                                  case 1:
                                  case 2:
                                  case 3:
                                  case 4:
                                  case 5:
                                  case 6:
                                    var input = document.createElement("input");
                                    celda.appendChild(input);
                                    break;
                                  default:
                                    //Statements executed when none of the values match the value of the expression
                                    break;
                              }


                              tr.appendChild(celda);
                            }

                tabla.appendChild(tr);







				});
				});

EOD;
        $document =& JFactory::getDocument();
        $document->addScriptDeclaration($script);


		parent::display($tpl);
	}
}