<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

final class SessionsModel extends ListModel
{
    protected $filter_fields = [
        'id', 's.id',
        'event_title', 'e.title',
        'title', 's.title',
        'start_at', 's.start_at',
        'capacity', 's.capacity',
        'published', 's.published',
    ];

    protected function populateState($ordering = 's.start_at', $direction = 'ASC'): void
    {
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('s.*, e.title AS event_title')
            ->from($db->quoteName('#__decaroevents_sessions', 's'))
            ->join('INNER', $db->quoteName('#__decaroevents_events', 'e') . ' ON e.id = s.event_id');

        $search = trim((string) $this->getState('filter.search'));
        if ($search !== '') {
            $searchTitle = '%' . $db->escape($search, true) . '%';
            $searchEvent = $searchTitle;
            $searchLocation = $searchTitle;
            $query->where('(' . $db->quoteName('s.title') . ' LIKE :searchTitle OR ' . $db->quoteName('e.title') . ' LIKE :searchEvent OR ' . $db->quoteName('s.location') . ' LIKE :searchLocation)')
                ->bind(':searchTitle', $searchTitle)
                ->bind(':searchEvent', $searchEvent)
                ->bind(':searchLocation', $searchLocation);
        }

        $eventId = (int) $this->getState('filter.event_id');
        if ($eventId > 0) {
            $query->where($db->quoteName('s.event_id') . ' = :eventId')
                ->bind(':eventId', $eventId, ParameterType::INTEGER);
        }

        $published = $this->getState('filter.published');
        if ($published !== '' && $published !== null) {
            $published = (int) $published;
            $query->where($db->quoteName('s.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        }

        $orderCol = (string) $this->getState('list.ordering', 's.start_at');
        $orderDir = strtoupper((string) $this->getState('list.direction', 'ASC'));
        $allowed = ['s.id', 'e.title', 's.title', 's.start_at', 's.capacity', 's.published'];
        if (!in_array($orderCol, $allowed, true)) {
            $orderCol = 's.start_at';
        }
        if (!in_array($orderDir, ['ASC', 'DESC'], true)) {
            $orderDir = 'ASC';
        }

        return $query->order($orderCol . ' ' . $orderDir);
    }
}
