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
            <style>
              .span6{
                width: 450px
              }
              table{
                color: #777;
                font-size: 13px;
                font-weight: normal;
                line-height: 24px;
              }

              .bordes-box {
                border: 1px solid #ccc;
              }
            </style>
            <page>
                <table id="logo">
                    <tbody>
                        <tr>
                            <td style="width: 490px;">
                                <img width="200" src="images/logo_iecce.png">
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td style="color: rgb(68, 68, 68); display: inline-block;">
                                                <h3>
                                                    No. Mutuo
                                                    <span class="border-box">'.$orden->idMutuo.'</span>
                                                </h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="display: inline-block; color: rgb(68, 68, 68);">
                                                <h3>
                                                    No. Orden
                                                    <span class="border-box">'.$orden->numOrden.'</span>
                                                </h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table>
                <tr>
                        <td >
                            <h1 style="margin-bottom: 40px;">'.strtoupper(JText::_('LBL_ORDEN_DE_PRESTAMO')).'</h1>
                        </td>
                    </tr>
                </table>
                <table style="width: 400px">

                    <tr style="height: 27px;" >
                        <td class="span6">Fecha de Elaboracion: <strong>'.date('d-m-Y', $orden->fecha_elaboracion).'</strong></td>
                        <td class="span6">Fecha de Deposito: <strong>'.date('d-m-Y', $orden->fecha_deposito).'</strong></td>
                    </tr>

                    <tr style="height: 27px">
                        <td class="span6">Tasa: <strong>'.$orden->tasa.'</strong></td>
                        <td class="span6">Tipo Movimiento: <strong>'.$orden->tipo_movimiento.'</strong></td>
                    </tr>

                    <tr  style="height: 54px"><td>&nbsp;</td><td>&nbsp;</td></tr>

                    <tr style="height: 27px">
                        <td class="span6">Acreedor: <strong>'.$orden->acreedor.'</strong></td>
                        <td class="span6">Capital: <strong>'.$signoAcreedor.number_format($orden->capital,2).'</strong></td>
                    </tr>

                    <tr style="height: 27px">
                        <td class="span6">RFC: <strong>'.$orden->a_rfc.'</strong></td>
                        <td class="span6">Intereses: <strong>'.$signoAcreedor.number_format($orden->intereses,2).'</strong></td>
                    </tr>
                    <tr style="height: 27px">
                        <td></td>
                        <td>IVA: <strong>'.$signoAcreedor.number_format($orden->iva_intereses,2).'</strong></td>
                    </tr>

                    <tr  style="height: 54px"><td>&nbsp;</td><td>&nbsp;</td></tr>

                    <tr style="height: 27px">
                        <td class="span6">Deudor: <strong>'.$orden->deudor.'</strong></td>
                        <td class="span6">Capital: <strong>'.$signoDeudor.number_format($orden->capital,2).'</strong></td>
                    </tr>

                    <tr style="height: 27px">
                        <td class="span6">RFC: <strong>'.$orden->d_rfc.'</strong></td>
                        <td class="span6">interese: <strong>'.$signoDeudor.number_format($orden->intereses,2).'</strong></td>
                    </tr>

                    <tr style="height: 27px">
                        <td></td>
                        <td>IVA: <strong>'.$signoDeudor.number_format($orden->iva_intereses,2).'</strong></td>
                    </tr>

                    <tr  style="height: 34px"><td></td><td></td></tr>

                    <tr style="height: 27px">
                        <td class="span6"><h3>Importe de la cantidad:</h3> <h4>$'.number_format($orden->capital,2).'</h4></td>
                        <td class="span6"><h3>Importe en letra:</h3><h4>'.$number2word->toCurrency('$'.number_format($orden->capital,2)).'</h4></td>
                    </tr>
                </table>
                <table>
                    <tr style="margin-top: 40px;">
                        <td>
                            '.strtoupper(JText::_('LBL_ORDEN_AUTORIZACION')).'
                        </td>
                    </tr>
                </table>
                <div style="text-align: center">
                <br/><br/><br/><br/><br/>
                    <table style="margin-left: 203px">
                        <tbody>
                            <tr style="text-align= center;">
                                <td>_____________________________</td>
                                <td>&nbsp;</td>
                                <td>____________________________</td>
                            </tr>
                            <tr>
                                <td>(Nombre y Firma)<br>
                                    Apoderado Legal<br>
                                    Acreedor</td>
                                <td>&nbsp;</td>
                                <td>(Nombre y Firma)<br>
                                    Apoderado Legal<br>
                                    Deudor</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </page>';
        }
        return $html;
    }
}