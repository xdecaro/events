<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

final class EventTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decaroevents_events', 'id', $db);
    }

    public function check(): bool
    {
        $this->title = trim((string) $this->title);
        $this->end_at = trim((string) $this->end_at) !== '' ? $this->end_at : null;
        $this->capacity = max(0, (int) $this->capacity) ?: null;

        if ($this->title === '' || trim((string) $this->start_at) === '') {
            $this->setError('Title and start date are required.');
            return false;
        }
        if ($this->end_at && strtotime((string) $this->end_at) < strtotime((string) $this->start_at)) {
            $this->setError('End date cannot be before start date.');
            return false;
        }

        return true;
    }
}
