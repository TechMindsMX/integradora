<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 11-Mar-15
 * Time: 9:42 AM
 */

namespace Integralib;

defined('_JEXEC') or die('Restricted access');

abstract class Order {

	protected $id;
	protected $emisor;
	protected $receptor;
	protected $createdDate;
	protected $status;
	protected $totalAmount;
	protected $orderType;

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
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
	 * @param $order
	 */
	protected function setProjectSubprojectFromOrder( $order ) {
		$order->proyecto = isset($order->proyecto) ? $order->proyecto : $order->projectId;

		$proyectos = \getFromTimOne::getProyects( null, $order->proyecto );

		if (!isset($order->proyecto) || $order->proyecto == 0 ) {
			$this->proyecto    = '';
			$this->subproyecto = '';
		} elseif ( $proyectos[ $order->proyecto ]->parentId != 0 ) {
			$this->subproyecto = $proyectos[ $order->proyecto ];
			$proyecto          = \getFromTimOne::getProyects( null, $proyectos[ $order->proyecto ]->parentId );
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

}