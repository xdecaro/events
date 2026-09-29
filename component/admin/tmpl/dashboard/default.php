<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$e = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$date = static fn($value) => $value ? HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC5')) : '—';
$data = $this->dashboard;
?>
<div class="xdecaro-scope">
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <a class="text-decoration-none" href="<?= Route::_('index.php?option=com_decaroevents&view=events') ?>">
        <section class="xdecaro-card h-100">
          <div class="xdecaro-card__body">
            <div class="small text-muted mb-1"><?= Text::_('COM_DECAROEVENTS_EVENTS') ?></div>
            <div class="h2 mb-1"><?= (int) ($data['events'] ?? 0) ?></div>
            <div class="small"><?= Text::sprintf('COM_DECAROEVENTS_DASHBOARD_UPCOMING_COUNT', (int) ($data['upcoming_events'] ?? 0)) ?></div>
          </div>
        </section>
      </a>
    </div>
    <div class="col-6 col-xl-3">
      <a class="text-decoration-none" href="<?= Route::_('index.php?option=com_decaroevents&view=sessions') ?>">
        <section class="xdecaro-card h-100">
          <div class="xdecaro-card__body">
            <div class="small text-muted mb-1"><?= Text::_('COM_DECAROEVENTS_SESSIONS') ?></div>
            <div class="h2 mb-1"><?= (int) ($data['sessions'] ?? 0) ?></div>
            <div class="small"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_SESSIONS_HELP') ?></div>
          </div>
        </section>
      </a>
    </div>
    <div class="col-6 col-xl-3">
      <a class="text-decoration-none" href="<?= Route::_('index.php?option=com_decaroevents&view=registrations') ?>">
        <section class="xdecaro-card h-100">
          <div class="xdecaro-card__body">
            <div class="small text-muted mb-1"><?= Text::_('COM_DECAROEVENTS_REGISTRATIONS') ?></div>
            <div class="h2 mb-1"><?= (int) ($data['registrations'] ?? 0) ?></div>
            <div class="small"><?= Text::sprintf('COM_DECAROEVENTS_DASHBOARD_PENDING_COUNT', (int) ($data['pending'] ?? 0)) ?></div>
          </div>
        </section>
      </a>
    </div>
    <div class="col-6 col-xl-3">
      <a class="text-decoration-none" href="<?= Route::_('index.php?option=com_decaroevents&view=registrations') ?>">
        <section class="xdecaro-card h-100">
          <div class="xdecaro-card__body">
            <div class="small text-muted mb-1"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_CHECKIN') ?></div>
            <div class="h2 mb-1"><?= (int) ($data['checked_in'] ?? 0) ?></div>
            <div class="small"><?= Text::sprintf('COM_DECAROEVENTS_DASHBOARD_WAITLIST_COUNT', (int) ($data['waitlist'] ?? 0)) ?></div>
          </div>
        </section>
      </a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-xl-8">
      <section class="xdecaro-card h-100">
        <div class="xdecaro-card__header d-flex align-items-center justify-content-between gap-3">
          <h2 class="xdecaro-card__title mb-0"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_NEXT_EVENTS') ?></h2>
          <a class="xdecaro-button" href="<?= Route::_('index.php?option=com_decaroevents&view=events') ?>"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_VIEW_ALL') ?></a>
        </div>
        <div class="xdecaro-card__body">
          <?php if (empty($data['next_events'])) : ?>
            <div class="alert alert-info mb-0"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_NO_UPCOMING') ?></div>
          <?php else : ?>
            <div class="d-grid gap-3">
              <?php foreach ($data['next_events'] as $event) : ?>
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 py-2 border-bottom">
                  <div>
                    <a class="fw-semibold" href="<?= Route::_('index.php?option=com_decaroevents&task=event.edit&id=' . (int) $event->id) ?>"><?= $e($event->title) ?></a>
                    <div class="small text-muted"><?= $e($event->location ?: '—') ?></div>
                  </div>
                  <div class="text-md-end">
                    <div><?= $date($event->start_at) ?></div>
                    <div class="small text-muted"><?= (int) $event->capacity > 0 ? Text::sprintf('COM_DECAROEVENTS_DASHBOARD_CAPACITY', (int) $event->capacity) : Text::_('COM_DECAROEVENTS_UNLIMITED') ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </div>
    <div class="col-xl-4">
      <section class="xdecaro-card h-100">
        <div class="xdecaro-card__header"><h2 class="xdecaro-card__title"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_QUICK_ACTIONS') ?></h2></div>
        <div class="xdecaro-card__body d-grid gap-2">
          <a class="xdecaro-button xdecaro-button--primary" href="<?= Route::_('index.php?option=com_decaroevents&task=event.add') ?>"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_NEW_EVENT') ?></a>
          <a class="xdecaro-button" href="<?= Route::_('index.php?option=com_decaroevents&task=session.add') ?>"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_NEW_SESSION') ?></a>
          <a class="xdecaro-button" href="<?= Route::_('index.php?option=com_decaroevents&task=registration.add') ?>"><?= Text::_('COM_DECAROEVENTS_DASHBOARD_NEW_REGISTRATION') ?></a>
          <a class="xdecaro-button" href="<?= Route::_('index.php?option=com_decaroevents&view=information') ?>"><?= Text::_('COM_DECAROEVENTS_INFORMATION') ?></a>
        </div>
      </section>
    </div>
  </div>
</div>
