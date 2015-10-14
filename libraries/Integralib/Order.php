<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 11-Mar-15
 * Time: 9:42 AM
 */

namespace Integralib;

use Catalogos;
use getFromTimOne;

defined('_JEXEC') or die('Restricted access');

abstract class Order {

	public $txs;
	protected $id;
	protected $emisor;
	protected $receptor;
    protected $totalAmount;
    protected $orderType;
    public $status;
    public $createdDate;

    /**
	 * @return mixed
	 */
	public function getId() {
		return (INT)$this->id;
	}

	/**
	 * @return mixed
	 */
	public function getEmisor() {
		return $this->emisor;
	}

	/**
	 * @return mixed
	 */
	public function getReceptor() {
		return $this->receptor;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedDate() {
		return $this->createdDate;
	}

	/**
	 * @param $order
	 *
	 * @return void
	 */
	abstract protected function setEmisor($order);

	/**
	 * @param $order
	 *
	 * @return void
	 */
	abstract protected function setReceptor($order);

	/**
	 * @return mixed
	 */
	public function getTotalAmount() {
		return (FLOAT)$this->totalAmount;
	}

	/**
	 * @return string
	 */
	public function getOrderType() {
		return $this->orderType;
	}

	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
	 * @param $order
	 */
	protected function setProjectSubprojectFromOrder( $order ) {
        if(isset($order->proyecto)) {
            $order->proyecto = $order->proyecto;
        }else{
            if($order->projectId2 == 0){
                $order->proyecto = $order->projectId;
            }else{
                $order->proyecto = $order->projectId2;
            }
        }

		$proyectos = getFromTimOne::getProyects( null, $order->proyecto );

		if (!isset($order->proyecto) || $order->proyecto == 0 ) {
			$this->proyecto    = '';
			$this->subproyecto = '';
		} elseif ( $proyectos[ $order->proyecto ]->parentId != 0 ) {
			$this->subproyecto = $proyectos[ $order->proyecto ];
			$proyecto          = getFromTimOne::getProyects( null, $proyectos[ $order->proyecto ]->parentId );
			$this->proyecto    = $proyecto[ $this->subproyecto->parentId ];
		} elseif ($order->proyecto != 0) {
			$this->proyecto    = $proyectos[ $order->proyecto ];
			$this->subproyecto = '';
		}

	}

	public function getProjectName() {
		$return = isset($this->proyecto->name) ? $this->proyecto->name : '';

		return $return;
	}

	public function getSubProjectName() {
		$return = isset($this->subproyecto->name) ? $this->subproyecto->name : '';

		return $return;
	}

	public function setTxsByUuid() {
		foreach ( $this->txs as $key => $val ) {
			$this->txs[$val->idTx] = $val;
			unset($this->txs[$key]);
		}

	}

	/**
	 * @return bool
     */
	public function isAuthorized()
	{
		$orderStatus = $this->getStatus();

		return in_array($orderStatus->id, array(5,8));
	}

	/**
	 * @param $comisiones
	 * @return float|null
     */
	public function calculaComision($comisiones)
	{
		$comision = getFromTimOne::getAplicableComision($this->orderType, $comisiones);

		// TODO: verificar $orden->totalAmount con el comprobante del xml
		$catalogo = new Catalogos();

		$ivas = (int)$catalogo->getFullIva();

		$montoComision = isset($comision) ? (FLOAT) $this->totalAmount * ((FLOAT)$comision->rate / 100) * (1+($ivas/100)) : null;

		return $montoComision;
	}
}