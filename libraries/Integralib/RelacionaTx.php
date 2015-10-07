<?php
/**
 * Created by PhpStorm.
 * User: Lutek
 * Date: 06/10/2015
 * Time: 03:57 PM
 */

namespace Integralib;
use JFactory;

class RelacionaTx
{
    public function asociacionTxs($txTimone, $id_tx_banco, $integradoId)
    {
        $db          = JFactory::getDbo();
        $query       = $db->getQuery(true);

        $values = array($db->quote($txTimone), $db->quote(time()), $db->quote($integradoId));

        $query->insert($db->quoteName('#__txs_timone_mandato'));
        $query->columns($db->quoteName(array('idTx', 'date', 'integradoId')));
        $query->values(implode(',',$values));

        $db->setQuery($query);
        $db->execute();
        $id_tx_timone = $db->insertid();

        $query = $db->getQuery(true);

        $values = array($db->quote($id_tx_banco), $db->quote($id_tx_timone));

        $query->insert($db->quoteName('#__txs_banco_timone_relation'));
        $query->columns($db->quoteName(array('id_txs_banco', 'id_txs_timone')));
        $query->values(implode(',',$values));

        $db->setQuery($query);
        $db->execute();
    }
}