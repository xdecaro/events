<?php
namespace Xdecaro\Component\Decaroevents\Site\Model; defined('_JEXEC') or die; use Joomla\CMS\Factory; use Joomla\CMS\MVC\Model\ListModel; use Joomla\Database\ParameterType;
final class EventsModel extends ListModel { protected function getListQuery(){ $db=$this->getDatabase();$levels=Factory::getApplication()->getIdentity()->getAuthorisedViewLevels() ?: [1];$q=$db->getQuery(true)->select('e.*')->from($db->quoteName('#__decaroevents_events','e'))->where('e.published = 1')->whereIn('e.access',array_map('intval',$levels),ParameterType::INTEGER)->order('e.start_at ASC');return $q;} }
