<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class PlgSampledataAtomicInstallerScript
{
    private function enable(): void
    {
        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('enabled') . ' = 1')
                ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                ->where($db->quoteName('folder') . ' = ' . $db->quote('sampledata'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('atomic'));

            $db->setQuery($query)->execute();
        } catch (\Throwable $e) {
            // Don't block installation.
        }
    }

    public function install($parent): void { $this->enable(); }
    public function update($parent): void { $this->enable(); }
    public function postflight($type, $parent): void { $this->enable(); }
}
