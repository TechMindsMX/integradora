<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 11-Mar-15
 * Time: 9:42 AM
 */

namespace Integralib;


abstract class Order {

	protected $id;
	protected $emisor;
	protected $receptor;
	protected $createdDate;

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

}