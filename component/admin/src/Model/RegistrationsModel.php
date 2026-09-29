<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

final class RegistrationsModel extends ListModel
{
    protected $filter_fields = [
        'id', 'r.id',
        'event_title', 'e.title',
        'name', 'r.name',
        'email', 'r.email',
        'status', 'r.status',
        'checked_in_at', 'r.checked_in_at',
        'created', 'r.created',
    ];

    protected function populateState($ordering = 'r.created', $direction = 'DESC'): void
    {
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('r.*, e.title AS event_title, s.title AS session_title')
            ->from($db->quoteName('#__decaroevents_registrations', 'r'))
            ->join('INNER', $db->quoteName('#__decaroevents_events', 'e') . ' ON e.id = r.event_id')
            ->join('LEFT', $db->quoteName('#__decaroevents_sessions', 's') . ' ON s.id = r.session_id');

        $search = trim((string) $this->getState('filter.search'));
        if ($search !== '') {
            $searchName = '%' . $db->escape($search, true) . '%';
            $searchEmail = $searchName;
            $searchEvent = $searchName;
            $query->where('(' . $db->quoteName('r.name') . ' LIKE :searchName OR ' . $db->quoteName('r.email') . ' LIKE :searchEmail OR ' . $db->quoteName('e.title') . ' LIKE :searchEvent)')
                ->bind(':searchName', $searchName)
                ->bind(':searchEmail', $searchEmail)
                ->bind(':searchEvent', $searchEvent);
        }

        $eventId = (int) $this->getState('filter.event_id');
        if ($eventId > 0) {
            $query->where($db->quoteName('r.event_id') . ' = :eventId')
                ->bind(':eventId', $eventId, ParameterType::INTEGER);
        }

        $status = trim((string) $this->getState('filter.status'));
        if ($status !== '') {
            $query->where($db->quoteName('r.status') . ' = :status')
                ->bind(':status', $status, ParameterType::STRING);
        }

        $checkedIn = $this->getState('filter.checked_in');
        if ($checkedIn === '1' || $checkedIn === 1) {
            $query->where($db->quoteName('r.checked_in_at') . ' IS NOT NULL');
        } elseif ($checkedIn === '0' || $checkedIn === 0) {
            $query->where($db->quoteName('r.checked_in_at') . ' IS NULL');
        }

        $orderCol = (string) $this->getState('list.ordering', 'r.created');
        $orderDir = strtoupper((string) $this->getState('list.direction', 'DESC'));
        $allowed = ['r.id', 'e.title', 'r.name', 'r.email', 'r.status', 'r.checked_in_at', 'r.created'];
        if (!in_array($orderCol, $allowed, true)) {
            $orderCol = 'r.created';
        }
        if (!in_array($orderDir, ['ASC', 'DESC'], true)) {
            $orderDir = 'DESC';
        }

        return $query->order($orderCol . ' ' . $orderDir);
    }
}
