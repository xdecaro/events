<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

/** Joomla resolves pkg_decaroevents to this exact legacy installer class name. */
final class pkg_decaroeventsInstallerScript
{
    private const MINIMUM_CORE = '1.3.0';

    public function preflight($type, $parent): bool
    {
        if ($type === 'uninstall') {
            return true;
        }

        $version = $this->coreVersion();

        if ($version !== '' && version_compare($version, self::MINIMUM_CORE, '>=')) {
            return true;
        }

        Factory::getApplication()->enqueueMessage(
            'Events by xdecaro requires Core by xdecaro ' . self::MINIMUM_CORE . ' or later.',
            'error'
        );

        return false;
    }

    private function coreVersion(): string
    {
        if (class_exists(\xdecaro\Core\Version::class)) {
            return trim((string) \xdecaro\Core\Version::VERSION);
        }

        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select($db->quoteName('manifest_cache'))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' = ' . $db->quote('package'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('pkg_xdecarocore'));
            $manifest = json_decode((string) $db->setQuery($query, 0, 1)->loadResult(), true);

            return is_array($manifest) ? trim((string) ($manifest['version'] ?? '')) : '';
        } catch (\Throwable) {
            return '';
        }
    }
}
