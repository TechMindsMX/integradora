<?php

defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');


class ReportesModelReportes extends JModelItem
{
	protected $msg;

	public function getMsg()
	{
		if (!isset($this->msg))
		{
			$jinput = JFactory::getApplication()->input;
			$id     = $jinput->get('id', 1, 'INT');

			switch ($id)
			{
				case 2:
					$this->msg = 'Good bye World!';
					break;
				default:
				case 1:
					$this->msg = 'data transmitido!';
					break;
			}
		}

		return $this->msg;
	}
}