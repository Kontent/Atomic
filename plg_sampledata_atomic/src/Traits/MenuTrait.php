<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kontent\Plugin\SampleData\Atomic\Traits;

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\Database\ParameterType;

/**
 * Menu type and menu item helpers for the Atomic sample data plugin.
 */
trait MenuTrait
{
    private function menuItemExists(string $menuType, string $aliasToFind): bool
    {
        $db = $this->getDatabase();
        $mt = $menuType;
        $alias = $aliasToFind;

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('menutype') . ' = :mt')
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':mt', $mt, ParameterType::STRING)
            ->bind(':alias', $alias, ParameterType::STRING);

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }

    private function getMenuItemIdByAlias(string $menuType, string $aliasToFind): int
    {
        $db = $this->getDatabase();
        $mt = $menuType;
        $alias = $aliasToFind;

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__menu'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('menutype') . ' = :mt')
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':mt', $mt, ParameterType::STRING)
            ->bind(':alias', $alias, ParameterType::STRING);

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    private function createChildMenuItem(string $menuType, string $title, int $parentId, string $link): void
    {
        /** @var \Joomla\Component\Menus\Administrator\Model\ItemModel $itemModel */
        $itemModel = $this->getApplication()->bootComponent('com_menus')->getMVCFactory()
            ->createModel('Item', 'Administrator', ['ignore_request' => true]);

        $user  = $this->getApplication()->getIdentity();
        $alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($title);

        // Strip fragment (#hash) from link for Joomla's component lookup
        $cleanLink = preg_replace('/#.*$/', '', $link);

        $data = [
            'id'              => 0,
            'menutype'        => $menuType,
            'title'           => $title,
            'alias'           => $alias,
            'link'            => $cleanLink,
            'type'            => 'component',
            'component_id'    => $this->getContentComponentId(),
            'published'       => 1,
            'parent_id'       => $parentId,
            'level'           => 2,
            'home'            => 0,
            'language'        => '*',
            'access'          => (int) $this->getApplication()->get('access', 1),
            'created_user_id' => (int) ($user->id ?? 0),
            'note'            => '',
            'img'             => '',
            'associations'    => [],
            'client_id'       => 0,
            'browserNav'      => 0,
            'template_style_id' => 0,
            'params'          => [],
        ];

        if (!$itemModel->save($data)) {
            throw new \RuntimeException($itemModel->getError());
        }
    }

    private function ensureMenuType(): string
    {
        $db = $this->getDatabase();
        $main = 'mainmenu';

        $query = $db->getQuery(true)
            ->select($db->quoteName('menutype'))
            ->from($db->quoteName('#__menu_types'))
            ->where($db->quoteName('menutype') . ' = :mt')
            ->bind(':mt', $main, ParameterType::STRING);

        $db->setQuery($query);

        if ($db->loadResult()) {
            return $main;
        }

        $mt = 'mainmenu-atomic';

        $q2 = $db->getQuery(true)
            ->select($db->quoteName('menutype'))
            ->from($db->quoteName('#__menu_types'))
            ->where($db->quoteName('menutype') . ' = :mt')
            ->bind(':mt', $mt, ParameterType::STRING);

        $db->setQuery($q2);

        if ($db->loadResult()) {
            return $mt;
        }

        $insert = (object) [
            'menutype'    => $mt,
            'title'       => 'Main Menu (Atomic)',
            'description' => 'Sample menu created by Atomic sample data',
            'client_id'   => 0,
        ];

        $db->insertObject('#__menu_types', $insert);

        return $mt;
    }

    private function getContentComponentId(): int
    {
        $record = ExtensionHelper::getExtensionRecord('com_content', 'component');

        return $record ? (int) $record->extension_id : 0;
    }

    private function createArticleMenuItem(string $menuType, string $title, int $articleId, bool $isHome): void
    {
        /** @var \Joomla\Component\Menus\Administrator\Model\ItemModel $itemModel */
        $itemModel = $this->getApplication()->bootComponent('com_menus')->getMVCFactory()
            ->createModel('Item', 'Administrator', ['ignore_request' => true]);

        $user  = $this->getApplication()->getIdentity();
        $alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($title);

        $data = [
            'id'              => 0,
            'menutype'        => $menuType,
            'title'           => $title,
            'alias'           => $alias,
            'link'            => 'index.php?option=com_content&view=article&id=' . (int) $articleId,
            'type'            => 'component',
            'component_id'    => $this->getContentComponentId(),
            'published'       => 1,
            'parent_id'       => 1,
            'level'           => 1,
            'home'            => $isHome ? 1 : 0,
            'language'        => '*',
            'access'          => (int) $this->getApplication()->get('access', 1),
            'created_user_id' => (int) ($user->id ?? 0),
            'note'            => '',
            'img'             => '',
            'associations'    => [],
            'client_id'       => 0,
            'browserNav'      => 0,
            'template_style_id' => 0,
            'params'          => [],
        ];

        if (!$itemModel->save($data)) {
            throw new \RuntimeException($itemModel->getError());
        }
    }

    private function createCategoryBlogMenuItem(string $menuType, string $title, int $catId, bool $isHome): void
    {
        /** @var \Joomla\Component\Menus\Administrator\Model\ItemModel $itemModel */
        $itemModel = $this->getApplication()->bootComponent('com_menus')->getMVCFactory()
            ->createModel('Item', 'Administrator', ['ignore_request' => true]);

        $user  = $this->getApplication()->getIdentity();
        $alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($title);

        $data = [
            'id'              => 0,
            'menutype'        => $menuType,
            'title'           => $title,
            'alias'           => $alias,
            'link'            => 'index.php?option=com_content&view=category&layout=blog&id=' . (int) $catId,
            'type'            => 'component',
            'component_id'    => $this->getContentComponentId(),
            'published'       => 1,
            'parent_id'       => 1,
            'level'           => 1,
            'home'            => $isHome ? 1 : 0,
            'language'        => '*',
            'access'          => (int) $this->getApplication()->get('access', 1),
            'created_user_id' => (int) ($user->id ?? 0),
            'note'            => '',
            'img'             => '',
            'associations'    => [],
            'client_id'       => 0,
            'browserNav'      => 0,
            'template_style_id' => 0,
            'params'          => [
                'num_leading_articles' => 1,
                'num_intro_articles'   => 0,
                'num_links'            => 0,
                'show_category_title'  => 0,
                'show_description'     => 0,
                'show_pagination'      => 0,
            ],
        ];

        if (!$itemModel->save($data)) {
            throw new \RuntimeException($itemModel->getError());
        }
    }
}
