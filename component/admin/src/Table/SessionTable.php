<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

final class SessionTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decaroevents_sessions', 'id', $db);
    }

    public function check(): bool
    {
        $this->event_id = (int) $this->event_id;
        $this->title = trim((string) $this->title);
        $this->end_at = trim((string) $this->end_at) !== '' ? $this->end_at : null;
        $this->capacity = max(0, (int) $this->capacity) ?: null;

        if ($this->event_id < 1 || $this->title === '' || trim((string) $this->start_at) === '') {
            $this->setError('Event, title and start date are required.');
            return false;
        }
        if ($this->end_at && strtotime((string) $this->end_at) < strtotime((string) $this->start_at)) {
            $this->setError('End date cannot be before start date.');
            return false;
        }

        return true;
    }
}
