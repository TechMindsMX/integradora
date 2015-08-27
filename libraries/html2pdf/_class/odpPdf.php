<?php
/**
 * Created by PhpStorm.
 * User: dev-ismael
 * Date: 27/08/2015
 * Time: 12:21 PM
 */


class odpPdf{

    public function __construct($data){
        $this->odp = $data;
        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');
        $this->integCurrent = new IntegradoSimple($this->integradoId);
    }


    function createHTML(){
        jimport('integradora.numberToWord');
        $number2word = new AifLibNumber;
        $orden = $this->odp;

        if ( strpos($orden->numOrden,'-0') ){
            $signoAcreedor = '-$';
            $signoDeudor = '$';
        }else{
            $signoAcreedor = '$';
            $signoDeudor = '-$';
        }
        $html ='';
        foreach ($this->odp as $key => $orden) {
            $html .='
            <page_header>
                <div class="clearfix" id="logo">
                    <div class="span5">
                        <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'" />
                    </div>
                    <div class="span5">
                        <div class="row">
                            <h3 class="span2 text-right" style="width: 40%;">No. Mutuo</h3>
                            <h3 class="span2 bordes-box text-center">'.$orden->idMutuo.'</h3>
                        </div>
                        <div class="row"><h3 class="span2 text-right" style="width: 40%;">No. Orden</h3><h3 class="span2 bordes-box text-center">'.$orden->numOrden.'</h3></div>
                    </div>
                </div>
            </page_header>
            <page>
            <h1 style="margin-bottom: 40px;">'.strtoupper(JText::_('LBL_ORDEN_DE_PRESTAMO')).'</h1>

            <div>
                <div class="span6">Fecha de Elaboración: <strong>'.date('d-m-Y', $orden->fecha_elaboracion).'</strong></div>
                <div class="span6">Fecha de depósito: <strong>'.date('d-m-Y',$orden->fecha_deposito).'</strong></div>
            </div>

            <div>
                <div class="span6">Tasa: <strong>'.$orden->tasa.'</strong></div>
                <div class="span6">Tipo Movimiento: <strong>'.$orden->tipo_movimiento.'</strong></div>
            </div>

            <div class="clearfix">&nbsp;</div>

            <div>
                <div class="span6">Acreedor: <strong>'.$orden->acreedor.'</strong></div>
                <div class="span6">Capital: <strong>'.$signoAcreedor.number_format($orden->capital,2).'</strong></div>
            </div>

            <div>
                <div class="span6">RFC: <strong>'.$orden->a_rfc.'</strong></div>
                <div class="span6">Intereses: <strong>'.$signoAcreedor.number_format($orden->intereses,2).'</strong></div>
                <div class="clearfix"></div>
                <div class="span7" style="text-align: right;">IVA: <strong>'.$signoAcreedor.number_format($orden->iva_intereses,2).'</strong></div>
            </div>

            <br /><br />

            <div>
                <div class="span6">Deudor: <strong>'.$orden->deudor.'</strong></div>
                <div class="span6">Capital: <strong>'.$signoDeudor.number_format($orden->capital,2).'</strong></div>
            </div>

            <div>
                <div class="span6">RFC: <strong>'.$orden->d_rfc.'</strong></div>
                <div class="span6">interese: <strong>'.$signoDeudor.number_format($orden->intereses,2).'</strong></div>
                <div class="clearfix"></div>
                <div class="span7" style="text-align: right;">IVA: <strong>'.$signoDeudor.number_format($orden->iva_intereses,2).'</strong></div>
            </div>

            <div>
                <div class="span6"><h3>Importe de la cantidad:</h3> <h4>$'.number_format($orden->capital,2).'</h4></div>
                <div class="span6"><h3>Importe en letra:</h3><h4>'.$number2word->toCurrency('$'.number_format($orden->capital,2)).'</h4></div>
            </div>

            <div class="clearfix">&nbsp;</div>

            <div class="clearfix" style="margin-top: 40px;">
                AUTORIZO EXPRESAMENTE A INTEGRADORA DE EMPRENDIMIENTOS CULTURALES, S.A. DE C.V.,CONFORME A LOS ESTATUTOS, POLITICAS,
                REGLAS Y PROCEDIMIENTOS, A EFECTUAR LA OPERACIÓN DE PRÉSTAMO DESCRITA Y A NOMBRE Y CUENTA DE MI REPRESENTADA

                <table class="table-condensed table-mutuo">

                    <tr>
                        <td>_____________________________</td>
                        <td>&nbsp;</td>
                        <td>____________________________</td>
                    </tr>
                    <tr>
                        <td>(Nombre y Firma)<br />
                            Apoderado Legal<br />
                            Acreedor</td>
                        <td>&nbsp;</td>
                        <td>(Nombre y Firma)<br />
                            Apoderado Legal<br />
                            Deudor</td>
                    </tr>
                </table>
            </div>
        </page>';

        }

        return $html;
    }
}