<?php
namespace Xdecaro\Component\Decaroevents\Site\Model; defined('_JEXEC') or die; use Joomla\CMS\Factory; use Joomla\CMS\MVC\Model\BaseDatabaseModel; use Joomla\Database\ParameterType;
final class EventModel extends BaseDatabaseModel
{
    public function getItem():object { $id=Factory::getApplication()->input->getInt('id');$db=$this->getDatabase();$levels=Factory::getApplication()->getIdentity()->getAuthorisedViewLevels() ?: [1];$q=$db->getQuery(true)->select('*')->from($db->quoteName('#__decaroevents_events'))->where('id = :id')->where('published = 1')->whereIn('access',array_map('intval',$levels),ParameterType::INTEGER)->bind(':id',$id,ParameterType::INTEGER);$item=$db->setQuery($q)->loadObject();if(!$item)throw new \RuntimeException('Evento non trovato.',404);return $item;}
    public function getSessions():array { $id=Factory::getApplication()->input->getInt('id');$db=$this->getDatabase();$q=$db->getQuery(true)->select('*')->from($db->quoteName('#__decaroevents_sessions'))->where('event_id = :id')->where('published = 1')->order('start_at ASC')->bind(':id',$id,ParameterType::INTEGER);return $db->setQuery($q)->loadObjectList(); }
}
