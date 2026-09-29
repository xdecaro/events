<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$e = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$date = static fn($value) => $value ? HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC5')) : '—';
$statusClass = static fn(string $status) => match ($status) {
    'confirmed' => 'xdecaro-badge--success',
    'waitlist' => 'xdecaro-badge--warning',
    'cancelled' => 'xdecaro-badge--danger',
    default => '',
};
$listOrder = $this->escape((string) $this->state->get('list.ordering', 'r.created'));
$listDirn = $this->escape((string) $this->state->get('list.direction', 'DESC'));
?>
<form action="<?= Route::_('index.php?option=com_decaroevents&view=registrations') ?>" method="post" id="adminForm" name="adminForm">
  <div class="xdecaro-scope">
    <?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this]) ?>

    <?php if (empty($this->items)) : ?>
      <div class="alert alert-info"><?= Text::_('JGLOBAL_NO_MATCHING_RESULTS') ?></div>
    <?php else : ?>
      <div class="d-none d-md-block">
        <div class="xdecaro-table-wrap">
          <table class="xdecaro-table">
            <thead><tr>
              <th><?= HTMLHelper::_('grid.checkall') ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'COM_DECAROEVENTS_EVENT', 'e.title', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'COM_DECAROEVENTS_FIELD_NAME', 'r.name', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'JGLOBAL_EMAIL', 'r.email', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'JSTATUS', 'r.status', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'COM_DECAROEVENTS_FIELD_CHECKED_IN', 'r.checked_in_at', $listDirn, $listOrder) ?></th>
            </tr></thead>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
              <tr>
                <td><?= HTMLHelper::_('grid.id', $i, (int) $item->id) ?></td>
                <td><?= $e($item->event_title) ?></td>
                <td><a href="<?= Route::_('index.php?option=com_decaroevents&task=registration.edit&id=' . (int) $item->id) ?>"><?= $e($item->name) ?></a></td>
                <td><?= $e($item->email) ?></td>
                <td><span class="xdecaro-badge <?= $statusClass((string) $item->status) ?>"><?= Text::_('COM_DECAROEVENTS_STATUS_' . strtoupper((string) $item->status)) ?></span></td>
                <td><?= $date($item->checked_in_at) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="d-md-none d-grid gap-3">
        <?php foreach ($this->items as $item) : ?>
          <section class="xdecaro-card">
            <div class="xdecaro-card__body">
              <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <a class="fw-semibold" href="<?= Route::_('index.php?option=com_decaroevents&task=registration.edit&id=' . (int) $item->id) ?>"><?= $e($item->name) ?></a>
                <span class="xdecaro-badge <?= $statusClass((string) $item->status) ?>"><?= Text::_('COM_DECAROEVENTS_STATUS_' . strtoupper((string) $item->status)) ?></span>
              </div>
              <div class="small text-muted mb-1"><?= $e($item->event_title) ?></div>
              <div class="text-break"><?= $e($item->email) ?></div>
              <div class="small mt-2"><?= Text::_('COM_DECAROEVENTS_FIELD_CHECKED_IN') ?>: <?= $date($item->checked_in_at) ?></div>
            </div>
          </section>
        <?php endforeach; ?>
      </div>

      <?= $this->pagination->getListFooter() ?>
    <?php endif; ?>
  </div>
  <input type="hidden" name="task" value="">
  <input type="hidden" name="boxchecked" value="0">
  <?= HTMLHelper::_('form.token') ?>
</form>
