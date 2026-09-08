<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

final class EventsModel extends ListModel
{
    protected function populateState($ordering = 'start_at', $direction = 'ASC')
    {
        parent::populateState($ordering, $direction);
        $this->setState('filter.search', Factory::getApplication()->input->getString('filter_search'));
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select('e.*')->from($db->quoteName('#__decaroevents_events', 'e'));
        $search = trim((string) $this->getState('filter.search'));
        if ($search !== '') {
            $like = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(' . $db->quoteName('e.title') . ' LIKE ' . $like . ' OR ' . $db->quoteName('e.location') . ' LIKE ' . $like . ')');
        }
        return $query->order('e.start_at ASC');
    }
}
