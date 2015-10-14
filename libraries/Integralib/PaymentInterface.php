<?php
/**
 * Created by PhpStorm.
 * User: Lutek
 * Date: 13/10/2015
 * Time: 04:12 PM
 */

namespace Integralib;


interface PaymentInterface
{
    public function sendCreateTx();
}