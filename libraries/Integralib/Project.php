<?php
/**
 * Created by PhpStorm.
 * User: RicardoTIM
 * Date: 23-Mar-15
 * Time: 9:14 AM
 */

namespace Integralib;

class Project {

	protected $id;
	protected $parentId;
	public $name;
	public $description;
	protected $status;

	function __construct( $id = null ) {
		if ( isset( $id ) ) {
			$this->id = $id;

			$this->getProjectData();
		}
	}

	private function getProjectData() {
		$tmp = \getFromTimOne::getProyects(null, $this->id);

		foreach ( $tmp as $project ) {
			foreach ( $project as $key => $val ) {
				$this->$key = $val;
			}
		}
	}

	public function isSubProject() {
		return (INT)$this->parentId != 0;
	}

}