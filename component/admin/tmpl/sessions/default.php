<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$e = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$date = static fn($value) => $value ? HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC5')) : '—';
$listOrder = $this->escape((string) $this->state->get('list.ordering', 's.start_at'));
$listDirn = $this->escape((string) $this->state->get('list.direction', 'ASC'));
?>
<form action="<?= Route::_('index.php?option=com_decaroevents&view=sessions') ?>" method="post" id="adminForm" name="adminForm">
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
              <th><?= HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 's.title', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'COM_DECAROEVENTS_FIELD_START', 's.start_at', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'COM_DECAROEVENTS_FIELD_CAPACITY', 's.capacity', $listDirn, $listOrder) ?></th>
              <th><?= HTMLHelper::_('searchtools.sort', 'JSTATUS', 's.published', $listDirn, $listOrder) ?></th>
            </tr></thead>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
              <tr>
                <td><?= HTMLHelper::_('grid.id', $i, (int) $item->id) ?></td>
                <td><?= $e($item->event_title) ?></td>
                <td><a href="<?= Route::_('index.php?option=com_decaroevents&task=session.edit&id=' . (int) $item->id) ?>"><?= $e($item->title) ?></a></td>
                <td><?= $date($item->start_at) ?></td>
                <td><?= (int) $item->capacity > 0 ? (int) $item->capacity : Text::_('COM_DECAROEVENTS_INHERIT') ?></td>
                <td><span class="xdecaro-badge <?= (int) $item->published === 1 ? 'xdecaro-badge--success' : '' ?>"><?= Text::_((int) $item->published === 1 ? 'JPUBLISHED' : 'JUNPUBLISHED') ?></span></td>
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
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="cid[]" id="mobile-cb<?= (int) $item->id ?>" value="<?= (int) $item->id ?>" onclick="Joomla.isChecked(this.checked);">
                <label class="form-check-label small" for="mobile-cb<?= (int) $item->id ?>"><?= Text::_('JSELECT') ?></label>
              </div>
              <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <a class="fw-semibold" href="<?= Route::_('index.php?option=com_decaroevents&task=session.edit&id=' . (int) $item->id) ?>"><?= $e($item->title) ?></a>
                <span class="xdecaro-badge <?= (int) $item->published === 1 ? 'xdecaro-badge--success' : '' ?>"><?= Text::_((int) $item->published === 1 ? 'JPUBLISHED' : 'JUNPUBLISHED') ?></span>
              </div>
              <div class="small text-muted mb-1"><?= $e($item->event_title) ?></div>
              <div><?= $date($item->start_at) ?></div>
              <div class="small mt-2"><?= Text::_('COM_DECAROEVENTS_FIELD_CAPACITY') ?>: <?= (int) $item->capacity > 0 ? (int) $item->capacity : Text::_('COM_DECAROEVENTS_INHERIT') ?></div>
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
