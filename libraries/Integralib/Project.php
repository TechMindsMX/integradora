<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Mar-15
 * Time: 9:14 AM
 */

namespace Integralib;

use getFromTimOne;
use JFactory;
use JText;

class Project {

	protected $id_proyecto;
	protected $parentId;
	public $name;
	public $description;
	protected $status;
	protected $integradoId;

	function __construct( $id = null ) {
		if ( isset( $id ) ) {
			$this->id_proyyecto = $id;

			$this->getProjectData();
		}
	}

	private function getProjectData() {
		$tmp = \getFromTimOne::getProyects(null, $this->id_proyyecto);

		foreach ( $tmp as $project ) {
			foreach ( $project as $key => $val ) {
				$this->$key = $val;
			}
		}
	}

	/**
	 * @param $post
	 * @param $currentValidations
	 * @param $integradoId
	 *
	 * @return mixed
	 */
	public static function checkDuplicatedProjectNameForIntegrado( $post, $currentValidations, $integradoId ) {
		if ( isset( $integradoId ) ) {
			$integradoId = JFactory::getSession()->get( 'integradoId', null, 'integrado' );
		}

		$projects = getFromTimOne::getProyects( $integradoId );

		foreach ( $projects as $value ) {
			if ( strtoupper( $value->name ) == strtoupper( $post['name'] ) && $value->id_proyecto != $post['id_proyecto'] ) {
				$validacion['success'] = false;
				$validacion['msg']     = JText::_( 'ERROR_PROJECT_NAME_DUPLICATED' );
			}
		}
		$validacion = isset( $validacion ) ? $validacion : $currentValidations;

		return $validacion;
	}

	public function isSubProject() {
		return (INT)$this->parentId != 0;
	}

	/**
	 * @return mixed
	 */
	public function getIntegradoId() {
		return $this->integradoId;
	}

	/**
	 * @param mixed $integradoId
	 */
	public function setIntegradoId( $integradoId ) {
		$this->integradoId = $integradoId;
	}

	/**
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param mixed $status
	 */
	public function setStatus( $status ) {
		$this->status = $status;
	}

	/**
	 * @return mixed
	 */
	public function getParentId() {
		return $this->parentId;
	}

	/**
	 * @param mixed $parentId
	 */
	public function setParentId( $parentId ) {
		$this->parentId = $parentId;
	}

	public function save( $returnSaved = null ) {
		$db = \JFactory::getDbo();
		$table = '#__integrado_proyectos';
		$obj = (object)(array)$this;

		$return = $db->insertObject($table, $obj);

		if($returnSaved === true) {
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName($table))
				->where($db->quoteName('id_proyecto').' = '.$db->insertid() );
			$db->setQuery($query);

			$return = $db->loadObject(__CLASS__);
		}

		return $return;
	}

	/**
	 * @return null
	 */
	public function getIdProyyecto() {
		return $this->id_proyyecto;
	}

}