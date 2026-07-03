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
 * Module create/query helpers for the Atomic sample data plugin.
 */
trait ModuleTrait
{
    private function moduleExists(string $position, string $module, string $title): bool
    {
        return (bool) $this->getModuleId($position, $module, $title);
    }

    private function getModuleId(string $position, string $module, string $title): int
    {
        $db = $this->getDatabase();
        $pos = $position;
        $mod = $module;
        $t = $title;

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('position') . ' = :pos')
            ->where($db->quoteName('module') . ' = :mod')
            ->where($db->quoteName('title') . ' = :t')
            ->bind(':pos', $pos, ParameterType::STRING)
            ->bind(':mod', $mod, ParameterType::STRING)
            ->bind(':t', $t, ParameterType::STRING);

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    // ------------------------------------------------------------------
    // Module creation helpers
    // ------------------------------------------------------------------

    private function createMenuModule(string $title, string $position, string $menuType, int $showTitle, int $ordering, string $layout = '_:horizontal'): void
    {
        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => 0,
            'title'      => $title,
            'module'     => 'mod_menu',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => $showTitle,
            'ordering'   => $ordering,
            'language'   => '*',
            'params'     => [
                'menutype'        => $menuType,
                'startLevel'      => 1,
                'endLevel'        => 0,
                'showAllChildren' => 1,
                'layout'          => $layout,
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function createSidebarMenuModule(string $title, string $position, string $menuType, int $showTitle, int $ordering): void
    {
        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => 0,
            'title'      => $title,
            'module'     => 'mod_menu',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => $showTitle,
            'ordering'   => $ordering,
            'language'   => '*',
            'params'     => [
                'menutype'        => $menuType,
                'startLevel'      => 1,
                'endLevel'        => 1,
                'showAllChildren' => 0,
                'layout'          => '_:vertical',
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function createFinderModule(string $title, string $position, int $ordering): void
    {
        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => 0,
            'title'      => $title,
            'module'     => 'mod_finder',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => 0,
            'ordering'   => $ordering,
            'language'   => '*',
            'params'     => [
                'show_button' => 0,
                'show_label'  => 0,
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function createLoginModule(string $title, string $position, int $ordering, int $showTitle): void
    {
        // Update the existing module on re-runs instead of duplicating it
        $existingId = $this->getModuleId($position, 'mod_login', $title);

        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => $existingId,
            'title'      => $title,
            'module'     => 'mod_login',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => $showTitle,
            'ordering'   => $ordering,
            'language'   => '*',
            'params'     => [
                'greeting'  => 1,
                'name'      => 0,
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function createTagsModule(string $title, string $position, int $ordering, int $showTitle): void
    {
        // Update the existing module on re-runs instead of duplicating it
        $existingId = $this->getModuleId($position, 'mod_tags_popular', $title);

        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => $existingId,
            'title'      => $title,
            'module'     => 'mod_tags_popular',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => $showTitle,
            'ordering'   => $ordering,
            'language'   => '*',
            'params'     => [
                'maximum'     => 10,
                'order_value' => 'title',
                'layout'      => 'atomic:default',
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function createCustomModule(string $title, string $position, int $ordering, int $showTitle, string $html): void
    {
        $existingId = $this->getModuleId($position, 'mod_custom', $title);

        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => $existingId,
            'title'      => $title,
            'module'     => 'mod_custom',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => $showTitle,
            'ordering'   => $ordering,
            'language'   => '*',
            'content'    => $html,
            'params'     => [
                'prepare_content' => 1,
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    // ------------------------------------------------------------------
    // Ensure / upsert module helpers
    // ------------------------------------------------------------------

    private function ensureCustomModule(string $title, string $position, string $html, int $showTitle, int $ordering): void
    {
        if ($this->moduleExists($position, 'mod_custom', $title)) {
            return;
        }

        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => 0,
            'title'      => $title,
            'module'     => 'mod_custom',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => $showTitle,
            'ordering'   => $ordering,
            'language'   => '*',
            'content'    => $html,
            'params'     => [
                'prepare_content' => 1,
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function ensureMenuModule(string $title, string $position, string $menuType, int $showTitle, int $ordering): void
    {
        if ($this->moduleExists($position, 'mod_menu', $title)) {
            return;
        }

        $this->createMenuModule($title, $position, $menuType, $showTitle, $ordering);
    }

    private function ensureFinderModule(string $title, string $position, int $ordering): void
    {
        if ($this->moduleExists($position, 'mod_finder', $title)) {
            return;
        }
        $this->createFinderModule($title, $position, $ordering);
    }

    private function ensureBreadcrumbsModule(string $title, string $position, int $ordering): void
    {
        if ($this->moduleExists($position, 'mod_breadcrumbs', $title)) {
            return;
        }

        /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
        $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
            ->createModel('Module', 'Administrator', ['ignore_request' => true]);

        $data = [
            'id'         => 0,
            'title'      => $title,
            'module'     => 'mod_breadcrumbs',
            'position'   => $position,
            'client_id'  => 0,
            'published'  => 1,
            'access'     => (int) $this->getApplication()->get('access', 1),
            'showtitle'  => 0,
            'ordering'   => $ordering,
            'language'   => '*',
            'params'     => [
                'showHere' => 1,
                'showHome' => 1,
            ],
            'assignment' => 0,
        ];

        if (!$model->save($data)) {
            throw new \RuntimeException($model->getError());
        }
    }

    private function upsertCustomModule(string $title, string $position, string $html, int $showTitle, int $ordering): void
    {
        $db = $this->getDatabase();
        $pos = $position;
        $mod = 'mod_custom';
        $t   = $title;

        $query = $db->getQuery(true)
            ->select($db->quoteName(['id']))
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('client_id') . ' = 0')
            ->where($db->quoteName('position') . ' = :pos')
            ->where($db->quoteName('module') . ' = :mod')
            ->where($db->quoteName('title') . ' = :t')
            ->bind(':pos', $pos, ParameterType::STRING)
            ->bind(':mod', $mod, ParameterType::STRING)
            ->bind(':t', $t, ParameterType::STRING);

        $db->setQuery($query);
        $id = (int) $db->loadResult();

        if ($id) {
            // Update existing module content
            /** @var \Joomla\Component\Modules\Administrator\Model\ModuleModel $model */
            $model = $this->getApplication()->bootComponent('com_modules')->getMVCFactory()
                ->createModel('Module', 'Administrator', ['ignore_request' => true]);

            $item = $model->getItem($id);
            if (!$item) {
                return;
            }

            $data = [
                'id'        => $id,
                'title'     => $title,
                'module'    => 'mod_custom',
                'position'  => $position,
                'client_id' => 0,
                'published' => (int) $item->published,
                'access'    => (int) $item->access,
                'showtitle' => $showTitle,
                'ordering'  => $ordering,
                'language'  => (string) ($item->language ?: '*'),
                'content'   => $html,
                'params'    => [
                    'prepare_content' => 1,
                ],
                'assignment' => 0,
            ];

            if (!$model->save($data)) {
                throw new \RuntimeException($model->getError());
            }

            return;
        }

        $this->ensureCustomModule($title, $position, $html, $showTitle, $ordering);
    }
}
