<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

final class EventsModel extends ListModel
{
    protected $filter_fields = [
        'id', 'e.id',
        'title', 'e.title',
        'start_at', 'e.start_at',
        'location', 'e.location',
        'capacity', 'e.capacity',
        'published', 'e.published',
    ];

    protected function populateState($ordering = 'e.start_at', $direction = 'ASC'): void
    {
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('e.*')
            ->from($db->quoteName('#__decaroevents_events', 'e'));

        $search = trim((string) $this->getState('filter.search'));
        if ($search !== '') {
            $searchTitle = '%' . $db->escape($search, true) . '%';
            $searchLocation = $searchTitle;
            $query->where('(' . $db->quoteName('e.title') . ' LIKE :searchTitle OR ' . $db->quoteName('e.location') . ' LIKE :searchLocation)')
                ->bind(':searchTitle', $searchTitle)
                ->bind(':searchLocation', $searchLocation);
        }

        $published = $this->getState('filter.published');
        if ($published !== '' && $published !== null) {
            $published = (int) $published;
            $query->where($db->quoteName('e.published') . ' = :published')
                ->bind(':published', $published, \Joomla\Database\ParameterType::INTEGER);
        }

        $orderCol = (string) $this->getState('list.ordering', 'e.start_at');
        $orderDir = strtoupper((string) $this->getState('list.direction', 'ASC'));
        $allowed = ['e.id', 'e.title', 'e.start_at', 'e.location', 'e.capacity', 'e.published'];
        if (!in_array($orderCol, $allowed, true)) {
            $orderCol = 'e.start_at';
        }
        if (!in_array($orderDir, ['ASC', 'DESC'], true)) {
            $orderDir = 'ASC';
        }

        return $query->order($orderCol . ' ' . $orderDir);
    }
}
