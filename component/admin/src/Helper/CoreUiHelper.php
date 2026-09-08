<?php
namespace Xdecaro\Component\Decaroevents\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use RuntimeException;

final class CoreUiHelper
{
    public static function useComponents(): void
    {
        if (!class_exists(\Xdecaro\Core\Asset\AssetService::class)
            || !class_exists(\Xdecaro\Core\Version::class)
            || version_compare((string) \Xdecaro\Core\Version::VERSION, '1.1.0', '<')) {
            throw new RuntimeException('Events by xdecaro requires Core by xdecaro 1.1.0 or later.');
        }
        $service = new \Xdecaro\Core\Asset\AssetService();
        $wam = Factory::getApplication()->getDocument()->getWebAssetManager();
        if (!$service->useComponents($wam)) {
            throw new RuntimeException('Core by xdecaro Web Asset Manager assets are unavailable.');
        }
    }
}
