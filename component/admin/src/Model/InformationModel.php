<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Model; defined('_JEXEC') or die;
use Joomla\CMS\Factory; use Joomla\CMS\MVC\Model\BaseDatabaseModel; use Joomla\Database\DatabaseInterface;
final class InformationModel extends BaseDatabaseModel
{
    public function getInformation():array
    {
        $db=Factory::getContainer()->get(DatabaseInterface::class);$tables=array_flip($db->getTableList());$required=['#__decaroevents_events','#__decaroevents_sessions','#__decaroevents_registrations'];$checks=[];foreach($required as $t)$checks[]=['name'=>$t,'ok'=>isset($tables[$db->replacePrefix($t)])];
        $coreVersion=class_exists(\Xdecaro\Core\Version::class)?(string)\Xdecaro\Core\Version::VERSION:'';
        return ['version'=>'1.0.0','core'=>['version'=>$coreVersion,'compatible'=>$coreVersion!==''&&version_compare($coreVersion,'1.1.0','>='),'assets'=>class_exists(\Xdecaro\Core\Asset\AssetService::class),'api'=>class_exists(\Xdecaro\Core\Integration\EntityReference::class)&&class_exists(\Xdecaro\Core\Integration\RelationReference::class)],'diagnostics'=>$checks];
    }
}
