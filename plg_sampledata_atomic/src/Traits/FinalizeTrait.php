<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kontent\Plugin\SampleData\Atomic\Traits;

\defined('_JEXEC') or die;

use Joomla\Database\ParameterType;

/**
 * Template settings, home menu and module cleanup helpers for the Atomic sample data plugin.
 */
trait FinalizeTrait
{
    /**
     * Apply recommended Atomic template settings to the active template style.
     */
    private function applyTemplateSettings(): void
    {
        $db = $this->getDatabase();

        // Find the Atomic template style
        $query = $db->getQuery(true)
            ->select([$db->quoteName('id'), $db->quoteName('params')])
            ->from($db->quoteName('#__template_styles'))
            ->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->setLimit(1);

        $db->setQuery($query);
        $row = $db->loadObject();

        if (!$row) {
            return;
        }

        $params = json_decode($row->params, true) ?: [];

        // Merge sample data settings (bsthemes is filter="integer" in the manifest, so store an int)
        $sampleSettings = [
            'headercolumns'  => '6-6',
            'footercolumns'  => '4-4-4',
            'bsthemes'       => 1,
            'sitetitle'      => 'Atomic Template for Joomla',
            'sitedescription' => 'Powerful. Flexible. SEO friendly.',
            'logo'           => 'media/templates/site/atomic/images/logo.png',
            'copyright'      => '1',
        ];

        foreach ($sampleSettings as $key => $value) {
            $params[$key] = $value;
        }

        $json = json_encode($params);
        $id   = (int) $row->id;

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('params') . ' = ' . $db->quote($json))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();

        // Make Atomic the default site template
        // First, unset home on all other site template styles
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('home') . ' = 0')
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('id') . ' != :id2')
            ->bind(':id2', $id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();

        // Then set Atomic as default
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('home') . ' = 1')
            ->where($db->quoteName('id') . ' = :id3')
            ->bind(':id3', $id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Set "Show Page Heading" to Hide on the default Home menu item.
     */
    private function setHomeMenuPageHeading(): void
    {
        $db = $this->getDatabase();

        // Find the site home menu item
        $query = $db->getQuery(true)
            ->select([$db->quoteName('id'), $db->quoteName('params')])
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('home') . ' = 1')
            ->where($db->quoteName('client_id') . ' = 0')
            ->setLimit(1);

        $db->setQuery($query);
        $row = $db->loadObject();

        if (!$row) {
            return;
        }

        $params = json_decode($row->params, true) ?: [];
        $params['show_page_heading'] = 0;

        $json = json_encode($params);
        $id   = (int) $row->id;

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__menu'))
            ->set($db->quoteName('params') . ' = ' . $db->quote($json))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }

    // ------------------------------------------------------------------
    // Cleanup helpers
    // ------------------------------------------------------------------

    private function removeModulesInPositionExcept(string $position, array $keepTitles): void
    {
        $db = $this->getDatabase();
        $pos = $position;

        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('position') . ' = :pos')
            ->bind(':pos', $pos, ParameterType::STRING);

        $db->setQuery($query);
        $rows = (array) $db->loadObjectList();

        foreach ($rows as $row) {
            $title = (string) ($row->title ?? '');
            if (in_array($title, $keepTitles, true)) {
                continue;
            }

            $id = (int) ($row->id ?? 0);
            if (!$id) {
                continue;
            }

            $del = $db->getQuery(true)
                ->delete($db->quoteName('#__modules'))
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($del)->execute();

            $del2 = $db->getQuery(true)
                ->delete($db->quoteName('#__modules_menu'))
                ->where($db->quoteName('moduleid') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($del2)->execute();
        }
    }

    private function removeModulesInPositionsNotInList(array $allowedPositions, array $titlePrefixAllow): void
    {
        // Deletes modules in client_id=0 positions that are NOT in allowedPositions,
        // but only if the title starts with any value in titlePrefixAllow (so we only remove our own old sample modules).
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'position']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('client_id') . ' = 0');

        $db->setQuery($query);
        $rows = (array) $db->loadObjectList();

        foreach ($rows as $row) {
            $pos = (string) ($row->position ?? '');
            if (in_array($pos, $allowedPositions, true)) {
                continue;
            }

            $title = (string) ($row->title ?? '');
            $isOurs = false;

            foreach ($titlePrefixAllow as $prefix) {
                if (strpos($title, $prefix) === 0) {
                    $isOurs = true;
                    break;
                }
            }

            if (!$isOurs) {
                continue;
            }

            $id = (int) ($row->id ?? 0);
            if (!$id) {
                continue;
            }

            $del = $db->getQuery(true)
                ->delete($db->quoteName('#__modules'))
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($del)->execute();

            $del2 = $db->getQuery(true)
                ->delete($db->quoteName('#__modules_menu'))
                ->where($db->quoteName('moduleid') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($del2)->execute();
        }
    }
}
