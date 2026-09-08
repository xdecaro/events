<?php
defined('_JEXEC') or die; use Joomla\CMS\Factory; use Joomla\Database\DatabaseInterface;
final class PkgDecaroeventsInstallerScript
{
    private const MINIMUM_CORE='1.1.0';
    public function preflight($type,$parent):bool { if($type==='uninstall')return true;$v=$this->coreVersion();if($v!==''&&version_compare($v,self::MINIMUM_CORE,'>='))return true;Factory::getApplication()->enqueueMessage('Events by xdecaro requires Core by xdecaro '.self::MINIMUM_CORE.' or later.','error');return false;}
    private function coreVersion():string { if(class_exists(\Xdecaro\Core\Version::class))return trim((string)\Xdecaro\Core\Version::VERSION);try{$db=Factory::getContainer()->get(DatabaseInterface::class);$q=$db->getQuery(true)->select($db->quoteName('manifest_cache'))->from($db->quoteName('#__extensions'))->where($db->quoteName('type').' = '.$db->quote('package'))->where($db->quoteName('element').' = '.$db->quote('pkg_xdecarocore'));$m=json_decode((string)$db->setQuery($q,0,1)->loadResult(),true);return is_array($m)?trim((string)($m['version']??'')):'';}catch(\Throwable){return '';}}
}
