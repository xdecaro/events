<?php
namespace Xdecaro\Component\Decaroevents\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Xdecaro\Component\Decaroevents\Administrator\Helper\CoreUiHelper;

final class HtmlView extends BaseHtmlView
{
    public array $dashboard = [];

    public function display($tpl = null): void
    {
        CoreUiHelper::useComponents();
        $this->dashboard = (array) $this->get('Dashboard');

        ToolbarHelper::title(Text::_('COM_DECAROEVENTS_DASHBOARD'), 'calendar');
        parent::display($tpl);
    }
}
