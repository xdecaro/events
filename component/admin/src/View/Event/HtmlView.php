<?php
namespace Xdecaro\Component\Decaroevents\Administrator\View\Event; defined('_JEXEC') or die;
use Joomla\CMS\Language\Text; use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView; use Joomla\CMS\Toolbar\ToolbarHelper; use Xdecaro\Component\Decaroevents\Administrator\Helper\CoreUiHelper;
final class HtmlView extends BaseHtmlView { public $form; public $item; public function display($tpl=null):void{CoreUiHelper::useComponents();$this->form=$this->get('Form');$this->item=$this->get('Item');ToolbarHelper::title(Text::_('COM_DECAROEVENTS_EVENT'),'calendar');ToolbarHelper::apply('event.apply');ToolbarHelper::save('event.save');ToolbarHelper::cancel('event.cancel');parent::display($tpl);} }
