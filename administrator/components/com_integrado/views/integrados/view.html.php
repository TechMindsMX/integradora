<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.view');

/**
 *
 */
class IntegradoViewIntegrados extends JViewLegacy {

    protected $items;

    protected $pagination;

    protected $state;

    function display($tpl = null) {

        $items = $this->get('Items');
        $state = $this->get('State');
        $this->catalogos = $this->get('Catalogos');

        $pagination = $this->get('Pagination');

        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->items = $items;
        $this->pagination = $pagination;

        $this->addToolBar();

        $this->sortDirection = $state->get('list.direction');
        $this->sortColumn = $state->get('list.ordering');

        parent::display($tpl);

        $this->setDocument();
    }

    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_INTEGRADO_MANAGER_TITULO'));
//        JToolBarHelper::editList('integrado.edit', JText::_('COM_INTEGRADO_VALIDACION_INTEGRADOS'));
//        JToolBarHelper::editList('integradoparams.edit',JText::_('COM_INTEGRADO_PARAMETRIZACION'));
//
//        if (JFactory::getUser()->authorise('core.admin', 'com_integrado')) {
//            JToolBarHelper::preferences('com_integrado');
//        }
    }

    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_INTEGRADO_ADMINISTRATION'));
    }

}
