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
                width: 550px
              }
              .span5{
                width: 600px;
                text-align: center;
              }
            </style>
            <page>
                <table id="logo">
                    <tbody>
                        <tr>
                            <td style="width: 490px;">
                                <img width="200" src="'.JUri::base().'images/logo_iecce.png'.'">
                            </td>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td style="color: rgb(68, 68, 68); display: inline-block;">
                                                <h3>
                                                    No. Mutuo
                                                    <span style="border: 1px solid #ccc; color: #999;">'.$orden->idMutuo.'</span>
                                                </h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="display: inline-block; color: rgb(68, 68, 68);">
                                                <h3>
                                                    No. Orden
                                                    <span style="border: 1px solid #ccc; color: #999;">'.$orden->numOrden.'</span>
                                                </h3>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table style="width: 650px">
                    <tr>
                        <td>
                            <h1 style="margin-bottom: 40px;">'.strtoupper(JText::_('LBL_ORDEN_DE_PRESTAMO')).'</h1>
                        </td>
                    </tr>
                    <tr style="height: 27px;" >
                        <td class="span6" style="width: 50px">Fecha de Elaboracion: <strong>'.date('d-m-Y', $orden->fecha_elaboracion).'</strong></td>
                        <td class="span6">Fecha de depósito: <strong>'.date('d-m-Y',$orden->fecha_deposito).'</strong></td>
                    </tr>

                    <tr  style="height: 34px"><td></td><td></td></tr>

                    <tr style="height: 27px">
                        <td class="span6">Tasa: <strong>'.$orden->tasa.'</strong></td>
                        <td class="span6">Tipo Movimiento: <strong>'.$orden->tipo_movimiento.'</strong></td>
                    </tr>

                    <tr  style="height: 34px"><td></td><td></td></tr>

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

                    <tr  style="height: 34px"><td></td><td></td></tr>

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

                    <tr style="margin-top: 40px;" class="clearfix">
                        <td style="text-align: center;" colspan="2">
                            <br/><br/><br/><table style="margin-left: 203px">
                                <tbody>
                                    <tr>
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
                        </td>
                    </tr>
                </table>
            </page>';
        }
        return $html;
    }
}