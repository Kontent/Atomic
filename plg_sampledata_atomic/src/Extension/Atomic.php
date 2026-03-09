<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Sampledata.atomic
 */

namespace Kontent\Plugin\SampleData\Atomic\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;

/**
 * Sample Data - Atomic
 *
 * Joomla 5.4+ compatible sample data plugin using the same conventions as
 * the core sample data plugins and DPCalendar's sample data plugin:
 * - onSampledataGetOverview() returns stdClass
 * - onAjaxSampledataApplyStepX() returns array
 *
 * Creates 4 articles, 4 menu items, and ~22 modules across all Atomic
 * template positions to produce a complete, ready-to-use site layout.
 */
final class Atomic extends CMSPlugin
{
    use DatabaseAwareTrait;

    protected $autoloadLanguage = true;

    public function onSampledataGetOverview(): \stdClass
    {
        $data              = new \stdClass();
        $data->name        = $this->_name; // "atomic"
        $data->title       = Text::_('PLG_SAMPLEDATA_ATOMIC_OVERVIEW_TITLE');
        $data->description = Text::_('PLG_SAMPLEDATA_ATOMIC_OVERVIEW_DESC');
        $data->icon        = 'palette';
        $data->steps       = 6;

        return $data;
    }

    // ------------------------------------------------------------------
    // Step 1: Category "Atomic" + "Welcome to Atomic" article
    // ------------------------------------------------------------------

    public function onAjaxSampledataApplyStep1(): array
    {
        $app = $this->getApplication();

        if (!($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface)) {
            return [];
        }

        if ((string) $app->getInput()->get('type') !== $this->_name) {
            return [];
        }

        // Keep the response JSON-clean even if extensions emit warnings/notices.
        $level = ob_get_level();
        ob_start();

        try {
            if (!ComponentHelper::isEnabled('com_content')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_CONTENT'),
                ];
            }

            if ($this->alreadyInstalled()) {
                $this->cleanBuffers($level);

                return [
                    'success' => true,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ALREADY_INSTALLED'),
                ];
            }

            // Unflag all currently-featured articles so Atomic content takes priority
            $this->unflagAllFeaturedArticles();

            $catid = $this->ensureSampleCategory();
            $this->createWelcomeArticle($catid);

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP1_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            $this->cleanBuffers($level);

            return [
                'success' => false,
                'message' => Text::sprintf('PLG_SAMPLEDATA_ATOMIC_STEP_FAILED', 1, $e->getMessage()),
            ];
        }
    }

    // ------------------------------------------------------------------
    // Step 2: "Getting Started" + "Explore Atomic Features" + "Style Guide"
    // ------------------------------------------------------------------

    public function onAjaxSampledataApplyStep2(): array
    {
        $app = $this->getApplication();

        if (!($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface)) {
            return [];
        }

        if ((string) $app->getInput()->get('type') !== $this->_name) {
            return [];
        }

        $level = ob_get_level();
        ob_start();

        try {
            if (!ComponentHelper::isEnabled('com_content') || !ComponentHelper::isEnabled('com_categories')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_CONTENT'),
                ];
            }

            $catid = $this->ensureSampleCategory();

            if ($this->articleExists('getting-started')
                && $this->articleExists('explore-atomic-features')
                && $this->articleExists('style-guide')
            ) {
                $this->cleanBuffers($level);

                return [
                    'success' => true,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP2_ALREADY_INSTALLED'),
                ];
            }

            if (!$this->articleExists('getting-started')) {
                $this->createGettingStartedArticle($catid);
            }

            if (!$this->articleExists('explore-atomic-features')) {
                $this->createFeaturesArticle($catid);
            }

            if (!$this->articleExists('style-guide')) {
                $this->createStyleGuideArticle($catid);
            }

            // Create sample tags and assign to articles
            $tagIds = $this->ensureSampleTags();

            $welcomeId  = $this->getArticleIdByAlias('welcome-to-atomic');
            $gettingId  = $this->getArticleIdByAlias('getting-started');
            $featuresId = $this->getArticleIdByAlias('explore-atomic-features');
            $styleId    = $this->getArticleIdByAlias('style-guide');

            if ($welcomeId) {
                $this->assignTagsToArticle($welcomeId, [
                    $tagIds['bootstrap'] ?? 0,
                    $tagIds['layout'] ?? 0,
                ]);
            }

            if ($gettingId) {
                $this->assignTagsToArticle($gettingId, [
                    $tagIds['bootstrap'] ?? 0,
                    $tagIds['themes'] ?? 0,
                    $tagIds['fonts'] ?? 0,
                ]);
            }

            if ($featuresId) {
                $this->assignTagsToArticle($featuresId, [
                    $tagIds['layout'] ?? 0,
                    $tagIds['typography'] ?? 0,
                    $tagIds['themes'] ?? 0,
                ]);
            }

            if ($styleId) {
                $this->assignTagsToArticle($styleId, [
                    $tagIds['typography'] ?? 0,
                    $tagIds['fonts'] ?? 0,
                ]);
            }

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP2_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            $this->cleanBuffers($level);

            return [
                'success' => false,
                'message' => Text::sprintf('PLG_SAMPLEDATA_ATOMIC_STEP_FAILED', 2, $e->getMessage()),
            ];
        }
    }

    // ------------------------------------------------------------------
    // Step 3: Menu type + 4 menu items
    // ------------------------------------------------------------------

    public function onAjaxSampledataApplyStep3(): array
    {
        $app = $this->getApplication();

        if (!($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface)) {
            return [];
        }

        if ((string) $app->getInput()->get('type') !== $this->_name) {
            return [];
        }

        $level = ob_get_level();
        ob_start();

        try {
            if (!ComponentHelper::isEnabled('com_menus') || !ComponentHelper::isEnabled('com_content')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_MENUS'),
                ];
            }

            $menuType = $this->ensureMenuType();
            $catid    = $this->ensureSampleCategory();

            $gettingId  = $this->getArticleIdByAlias('getting-started');
            $featuresId = $this->getArticleIdByAlias('explore-atomic-features');
            $styleId    = $this->getArticleIdByAlias('style-guide');

            // Home → Category Blog showing 1 intro article from the Atomic category
            if ($catid) {
                if (!$this->menuItemExists($menuType, 'home')) {
                    $this->createCategoryBlogMenuItem($menuType, 'Home', $catid, true);
                }
            }

            if ($gettingId) {
                if (!$this->menuItemExists($menuType, 'getting-started')) {
                    $this->createArticleMenuItem($menuType, 'Getting Started', $gettingId, false);
                }
            }

            if ($featuresId) {
                if (!$this->menuItemExists($menuType, 'features')) {
                    $this->createArticleMenuItem($menuType, 'Features', $featuresId, false);
                }
            }

            if ($styleId) {
                if (!$this->menuItemExists($menuType, 'style-guide')) {
                    $this->createArticleMenuItem($menuType, 'Style Guide', $styleId, false);
                }
            }

            // Create child menu items under Features for submenu example
            if ($featuresId) {
                $featuresMenuId = $this->getMenuItemIdByAlias($menuType, 'features');
                if ($featuresMenuId) {
                    if (!$this->menuItemExists($menuType, 'template-positions')) {
                        $this->createChildMenuItem(
                            $menuType,
                            'Template Positions',
                            $featuresMenuId,
                            'index.php?option=com_content&view=article&id=' . (int) $featuresId . '#positions'
                        );
                    }
                    if (!$this->menuItemExists($menuType, 'template-settings')) {
                        $this->createChildMenuItem(
                            $menuType,
                            'Template Settings',
                            $featuresMenuId,
                            'index.php?option=com_content&view=article&id=' . (int) $featuresId . '#settings'
                        );
                    }
                    if (!$this->menuItemExists($menuType, 'design-tokens')) {
                        $this->createChildMenuItem(
                            $menuType,
                            'Design Tokens',
                            $featuresMenuId,
                            'index.php?option=com_content&view=article&id=' . (int) $featuresId . '#design-tokens'
                        );
                    }
                }
            }

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP3_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            $this->cleanBuffers($level);

            return [
                'success' => false,
                'message' => Text::sprintf('PLG_SAMPLEDATA_ATOMIC_STEP_FAILED', 3, $e->getMessage()),
            ];
        }
    }

    // ------------------------------------------------------------------
    // Step 4: Navigation modules (8)
    // ------------------------------------------------------------------

    public function onAjaxSampledataApplyStep4(): array
    {
        $app = $this->getApplication();

        if (!($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface)) {
            return [];
        }

        if ((string) $app->getInput()->get('type') !== $this->_name) {
            return [];
        }

        $level = ob_get_level();
        ob_start();

        try {
            if (!ComponentHelper::isEnabled('com_modules') || !ComponentHelper::isEnabled('com_menus')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_MODULES'),
                ];
            }

            $menuType = $this->ensureMenuType();

            // Top Menu
            if (!$this->moduleExists('topmenu', 'mod_menu', 'Atomic Top Menu')) {
                $this->createMenuModule('Atomic Top Menu', 'topmenu', $menuType, 0, 1);
            }

            // Sidebar Menu
            if (!$this->moduleExists('sidebar-menu', 'mod_menu', 'Atomic Sidebar Menu')) {
                $this->createMenuModule('Atomic Sidebar Menu', 'sidebar-menu', $menuType, 0, 3);
            }

            // Mobile Menu
            if (!$this->moduleExists('mobilemenu', 'mod_menu', 'Atomic Mobile Menu')) {
                $this->createMenuModule('Atomic Mobile Menu', 'mobilemenu', $menuType, 0, 4);
            }

            // Search (placed in header-right)
            if (!$this->moduleExists('header-right', 'mod_finder', 'Atomic Search')) {
                $this->createFinderModule('Atomic Search', 'header-right', 5);
            }

            // Breadcrumbs
            if (!$this->moduleExists('breadcrumbs', 'mod_breadcrumbs', 'Atomic Breadcrumbs')) {
                $this->ensureBreadcrumbsModule('Atomic Breadcrumbs', 'breadcrumbs', 6);
            }

            // Main Top (custom)
            $mainTopHtml = '<div class="glass p-3">'
                . '<div class="section-label"><i class="fa-solid fa-fw fa-layer-group me-1"></i> Main Top</div>'
                . '<p class="small mb-0" style="color:var(--text-secondary)">The <code>main-top</code> position sits between the hero and the content columns. Use it for full-width banners or feature highlights.</p>'
                . '</div>';

            if (!$this->moduleExists('main-top', 'mod_custom', 'Atomic Main Top')) {
                $this->createCustomModule('Atomic Main Top', 'main-top', 7, 0, $mainTopHtml);
            }

            // Main Bottom (custom)
            $mainBottomHtml = '<div class="glass p-3">'
                . '<div class="section-label"><i class="fa-solid fa-fw fa-layer-group me-1"></i> Main Bottom</div>'
                . '<p class="small mb-0" style="color:var(--text-secondary)">The <code>main-bottom</code> position sits below the content columns. Use it for related content, newsletter signups, or secondary navigation.</p>'
                . '</div>';

            if (!$this->moduleExists('main-bottom', 'mod_custom', 'Atomic Main Bottom')) {
                $this->createCustomModule('Atomic Main Bottom', 'main-bottom', 8, 0, $mainBottomHtml);
            }

            // Left Body Sidebar Navigation (top-level items only, no submenus)
            if (!$this->moduleExists('leftbody', 'mod_menu', 'Atomic Sidebar Nav')) {
                $this->createSidebarMenuModule('Atomic Sidebar Nav', 'leftbody', $menuType, 1, 9);
            }

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP4_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            $this->cleanBuffers($level);

            return [
                'success' => false,
                'message' => Text::sprintf('PLG_SAMPLEDATA_ATOMIC_STEP_FAILED', 4, $e->getMessage()),
            ];
        }
    }

    // ------------------------------------------------------------------
    // Step 5: Position demo modules (13 custom HTML)
    // ------------------------------------------------------------------

    public function onAjaxSampledataApplyStep5(): array
    {
        $app = $this->getApplication();

        if (!($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface)) {
            return [];
        }

        if ((string) $app->getInput()->get('type') !== $this->_name) {
            return [];
        }

        $level = ob_get_level();
        ob_start();

        try {
            if (!ComponentHelper::isEnabled('com_modules') || !ComponentHelper::isEnabled('com_menus')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_MODULES'),
                ];
            }

            // --- Alert ---
            $alertHtml = '<div class="col-auto d-flex align-items-center justify-content-center gap-2">'
                . '<i class="fa-solid fa-rocket" aria-hidden="true"></i>'
                . ' Welcome to Atomic &mdash; a minimal Joomla template with 50+ settings.'
                . ' <a href="index.php?option=com_content&amp;view=article&amp;alias=getting-started" class="ms-1">Get started <i class="fa-solid fa-arrow-right fa-xs"></i></a>'
                . '</div>';

            $this->createCustomModule('Atomic Alert', 'alert', 1, 0, $alertHtml);

            // --- Hero ---
            $heroHtml = '<div class="hero-card-wrapper">'
                . '<div class="hero-card">'
                . '<p class="section-label mb-2"><i class="fa-solid fa-fw fa-atom me-1"></i> Atomic Template for Joomla</p>'
                . '<h2>A clean foundation<br>for <span class="gradient-text">any site</span>.</h2>'
                . '<p class="lead">50+ settings, Google Fonts, Bootswatch themes, light/dark mode, and flexible layouts &mdash; all from one minimal template.</p>'
                . '<div class="d-flex gap-3 mt-4 flex-wrap">'
                . '<a class="btn btn-accent" href="index.php?option=com_content&amp;view=article&amp;alias=getting-started"><i class="fa-solid fa-rocket me-2"></i>Get Started</a>'
                . '<a class="btn btn-glass" href="index.php?option=com_content&amp;view=article&amp;alias=explore-atomic-features"><i class="fa-solid fa-compass me-2"></i>Explore Features</a>'
                . '</div>'
                . '<div class="hero-stats">'
                . '<div class="hero-stat"><div class="stat-number">50+</div><div class="stat-label">Settings</div></div>'
                . '<div class="hero-stat"><div class="stat-number">17</div><div class="stat-label">Positions</div></div>'
                . '<div class="hero-stat"><div class="stat-number">9</div><div class="stat-label">Themes</div></div>'
                . '<div class="hero-stat"><div class="stat-number">1500+</div><div class="stat-label">Fonts</div></div>'
                . '</div>'
                . '</div>'
                . '</div>';

            $this->createCustomModule('Atomic Hero', 'hero', 4, 0, $heroHtml);

            // --- Featured (abovebody) ---
            $featuredHtml = '<div class="glass p-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">'
                . '<div>'
                . '<div class="section-label"><i class="fa-solid fa-fw fa-star me-1"></i> Featured</div>'
                . '<div style="color:var(--text-secondary)">Welcome to your new Joomla site &mdash; this is the <code>abovebody</code> position.</div>'
                . '</div>'
                . '<a class="btn btn-accent btn-sm" href="index.php?option=com_content">Browse articles</a>'
                . '</div>';

            $this->createCustomModule('Atomic Featured', 'abovebody', 5, 0, $featuredHtml);

            // --- Sidebar Info (leftbody) ---
            $sidebarInfoHtml = '<p class="small text-body-secondary">Atomic is a minimal, admin-friendly Joomla template. Clean defaults, flexible settings, and Bootstrap at the core.</p>'
                . '<p class="small text-body-secondary mb-0">This module is in the <code>leftbody</code> position. Assign any module here to create a left sidebar.</p>';

            $this->createCustomModule('Atomic Sidebar Info', 'leftbody', 6, 1, $sidebarInfoHtml);

            // --- Tags (leftbody) ---
            $tagsHtml = '<div class="d-flex flex-wrap gap-2">'
                . '<span class="tag">Bootstrap</span>'
                . '<span class="tag blue">Typography</span>'
                . '<span class="tag teal">Layout</span>'
                . '<span class="tag amber">Modules</span>'
                . '<span class="tag">Themes</span>'
                . '<span class="tag blue">Fonts</span>'
                . '</div>';

            $this->createCustomModule('Atomic Tags', 'leftbody', 7, 1, $tagsHtml);

            // --- Quick Links (rightbody) ---
            $quickLinksHtml = '<ul class="list-unstyled mb-0">'
                . '<li class="py-1"><a href="#" class="text-decoration-none d-flex align-items-center gap-2"><i class="fa-solid fa-book fa-fw" style="color:var(--accent-primary)" aria-hidden="true"></i> Documentation</a></li>'
                . '<li class="py-1"><a href="#" class="text-decoration-none d-flex align-items-center gap-2"><i class="fa-solid fa-code fa-fw" style="color:var(--accent-secondary)" aria-hidden="true"></i> GitHub Repository</a></li>'
                . '<li class="py-1"><a href="#" class="text-decoration-none d-flex align-items-center gap-2"><i class="fa-solid fa-palette fa-fw" style="color:var(--accent-tertiary)" aria-hidden="true"></i> Bootswatch Themes</a></li>'
                . '<li class="py-1"><a href="#" class="text-decoration-none d-flex align-items-center gap-2"><i class="fa-solid fa-font fa-fw" style="color:var(--accent-warm)" aria-hidden="true"></i> Google Fonts</a></li>'
                . '</ul>';

            $this->createCustomModule('Atomic Quick Links', 'rightbody', 8, 1, $quickLinksHtml);

            // --- Tip (rightbody) ---
            $tipHtml = '<p class="small mb-0" style="color:var(--text-secondary)"><i class="fa-solid fa-lightbulb me-1" style="color:var(--accent-warm)" aria-hidden="true"></i> Start with layout and typography, then fine-tune colors and modules. Enable <strong>Menu alias as body class</strong> in the Colors tab for easy per-page CSS targeting.</p>';

            $this->createCustomModule('Atomic Tip', 'rightbody', 9, 1, $tipHtml);

            // --- Next Step (belowbody) ---
            $nextStepHtml = '<div class="glass p-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">'
                . '<div>'
                . '<div class="fw-semibold" style="color:var(--text-primary)">Ready to build?</div>'
                . '<div class="small" style="color:var(--text-secondary)">This is the <code>belowbody</code> position, below the main content area.</div>'
                . '</div>'
                . '<a class="btn btn-accent btn-sm" href="index.php?option=com_content&amp;view=article&amp;alias=getting-started">Getting Started Guide</a>'
                . '</div>';

            $this->createCustomModule('Atomic Next Step', 'belowbody', 10, 0, $nextStepHtml);

            // --- Footer ---
            $footerHtml = '<div>'
                . '<div class="fw-bold mb-2" style="font-size:1.15rem;color:var(--accent-primary)">Atomic</div>'
                . '<p class="small mb-2" style="color:var(--text-secondary)">A minimal Joomla template with clean defaults, flexible settings, and Bootstrap at the core.</p>'
                . '<div class="d-flex gap-3 small">'
                . '<a href="#">Privacy</a>'
                . '<a href="#">Terms</a>'
                . '<a href="#">Sitemap</a>'
                . '</div>'
                . '</div>';

            $this->createCustomModule('Atomic Footer', 'footer', 11, 0, $footerHtml);

            // --- Footer Center ---
            $footerCenterHtml = '<h6>Resources</h6>'
                . '<ul>'
                . '<li><a href="#">Documentation</a></li>'
                . '<li><a href="#">Support</a></li>'
                . '<li><a href="#">Changelog</a></li>'
                . '<li><a href="#">GitHub</a></li>'
                . '</ul>';

            $this->createCustomModule('Atomic Footer Center', 'footer-center', 12, 0, $footerCenterHtml);

            // --- Footer Right ---
            $footerRightHtml = '<h6>About</h6>'
                . '<ul>'
                . '<li><a href="index.php?option=com_content&amp;view=article&amp;alias=explore-atomic-features">Features</a></li>'
                . '<li><a href="index.php?option=com_content&amp;view=article&amp;alias=style-guide">Style Guide</a></li>'
                . '<li><a href="index.php?option=com_content&amp;view=article&amp;alias=getting-started">Getting Started</a></li>'
                . '</ul>';

            $this->createCustomModule('Atomic Footer Right', 'footer-right', 13, 0, $footerRightHtml);

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP5_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            $this->cleanBuffers($level);

            return [
                'success' => false,
                'message' => Text::sprintf('PLG_SAMPLEDATA_ATOMIC_STEP_FAILED', 5, $e->getMessage()),
            ];
        }
    }

    // ------------------------------------------------------------------
    // Step 6: Enhanced Welcome article content + cleanup
    // ------------------------------------------------------------------

    public function onAjaxSampledataApplyStep6(): array
    {
        $app = $this->getApplication();

        if (!($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface)) {
            return [];
        }

        if ((string) $app->getInput()->get('type') !== $this->_name) {
            return [];
        }

        $level = ob_get_level();
        ob_start();

        try {
            if (!ComponentHelper::isEnabled('com_modules') || !ComponentHelper::isEnabled('com_content') || !ComponentHelper::isEnabled('com_menus')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_STEP6_REQUIREMENTS'),
                ];
            }

            // Component area sample content
            $this->ensureEnhancedWelcomeArticle();

            $menuType = $this->ensureMenuType();

            // Remove duplicates / unwanted modules that may come from other sample data
            $this->removeModulesInPositionExcept('breadcrumbs', ['Atomic Breadcrumbs']);
            $this->removeModulesInPositionExcept('footer', ['Atomic Footer']);

            // Remove any modules we created previously in non-supported positions (only removes titles starting with "Atomic")
            $this->removeModulesInPositionsNotInList([
                'alert', 'hero', 'topmenu', 'menu',
                'mobilemenu', 'sidebar-menu', 'breadcrumbs',
                'header', 'header-center', 'header-right',
                'abovebody', 'main-top', 'leftbody', 'rightbody', 'belowbody',
                'main-bottom', 'footer', 'footer-center', 'footer-right', 'debug',
                'topbar', 'below-top', 'banner', 'sidebar-left', 'sidebar-right',
                'top-a', 'top-b', 'bottom-a', 'bottom-b',
            ], ['Atomic']);

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP6_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            $this->cleanBuffers($level);

            return [
                'success' => false,
                'message' => Text::sprintf('PLG_SAMPLEDATA_ATOMIC_STEP_FAILED', 6, $e->getMessage()),
            ];
        }
    }

    // ==================================================================
    // Helper methods
    // ==================================================================

    private function cleanBuffers(int $level): void
    {
        while (ob_get_level() > $level) {
            @ob_end_clean();
        }
    }

    /**
     * Set featured = 0 on every article that is currently featured.
     * Also removes corresponding rows from #__content_frontpage.
     */
    private function unflagAllFeaturedArticles(): void
    {
        $db = $this->getDatabase();

        // Set featured = 0 in #__content for all currently featured articles
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__content'))
            ->set($db->quoteName('featured') . ' = 0')
            ->where($db->quoteName('featured') . ' = 1');
        $db->setQuery($query);
        $db->execute();

        // Remove all rows from #__content_frontpage
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__content_frontpage'));
        $db->setQuery($query);
        $db->execute();
    }

    private function alreadyInstalled(): bool
    {
        $db = $this->getDatabase();
        $alias = 'welcome-to-atomic';
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias, ParameterType::STRING);

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }

    private function ensureSampleCategory(): int
    {
        $db = $this->getDatabase();
        $alias = 'atomic';

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias, ParameterType::STRING);

        $db->setQuery($query);
        $existing = (int) $db->loadResult();

        if ($existing) {
            return $existing;
        }

        /** @var \Joomla\Component\Categories\Administrator\Model\CategoryModel $catModel */
        $catModel = $this->getApplication()->bootComponent('com_categories')->getMVCFactory()
            ->createModel('Category', 'Administrator', ['ignore_request' => true]);

        $user   = $this->getApplication()->getIdentity();
        $access = (int) $this->getApplication()->get('access', 1);

        $data = [
            'id'              => 0,
            'parent_id'       => 1,
            'extension'       => 'com_content',
            'title'           => 'Atomic',
            'alias'           => 'atomic',
            'published'       => 1,
            'language'        => '*',
            'access'          => $access,
            'created_user_id' => (int) ($user->id ?? 0),
            'params'          => '{}',
            'metadata'        => '{}',
        ];

        if (!$catModel->save($data)) {
            throw new \RuntimeException($catModel->getError());
        }

        $item = $catModel->getItem();

        return (int) ($item->id ?? 0);
    }

    private function articleExists(string $aliasToFind): bool
    {
        $db = $this->getDatabase();
        $alias = $aliasToFind;

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias, ParameterType::STRING);

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }

    private function getArticleIdByAlias(string $aliasToFind): int
    {
        $db = $this->getDatabase();
        $alias = $aliasToFind;

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias, ParameterType::STRING);

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

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
    // Article creation helpers
    // ------------------------------------------------------------------

    private function createWelcomeArticle(int $catid): void
    {
        /** @var \Joomla\Component\Content\Administrator\Model\ArticleModel $articleModel */
        $articleModel = $this->getApplication()->bootComponent('com_content')->getMVCFactory()
            ->createModel('Article', 'Administrator', ['ignore_request' => true]);

        $user   = $this->getApplication()->getIdentity();
        $access = (int) $this->getApplication()->get('access', 1);

        $introtext = '<p class="lead">Atomic is a clean, minimal Joomla template with 50+ settings, flexible layouts, and Bootstrap 5.3 at its core. This sample data gives you a ready-to-use starting point.</p>';

        $fulltext = '<div class="row g-4 mb-5">'
            . '<div class="col-md-4">'
            . '<div class="card h-100 text-center border">'
            . '<div class="card-body">'
            . '<div class="display-6 text-primary mb-3"><i class="fa-solid fa-sliders"></i></div>'
            . '<h3 class="h5 card-title">50+ Settings</h3>'
            . '<p class="card-text small text-body-secondary">Typography, colors, layout columns, sticky header, social meta, and custom code injection &mdash; all from the template options.</p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-4">'
            . '<div class="card h-100 text-center border">'
            . '<div class="card-body">'
            . '<div class="display-6 text-primary mb-3"><i class="fa-solid fa-palette"></i></div>'
            . '<h3 class="h5 card-title">Bootswatch Themes</h3>'
            . '<p class="card-text small text-body-secondary">Switch between built-in Bootswatch themes or load your own custom Bootstrap build with a single setting.</p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-4">'
            . '<div class="card h-100 text-center border">'
            . '<div class="card-body">'
            . '<div class="display-6 text-primary mb-3"><i class="fa-solid fa-font"></i></div>'
            . '<h3 class="h5 card-title">Google Fonts</h3>'
            . '<p class="card-text small text-body-secondary">Choose any Google Font for headings and body text. Atomic loads them automatically via CSS custom properties.</p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="mb-4">'
            . '<h3 class="h4 fw-bold mb-3">How This Page Is Built</h3>'
            . '<p class="text-body-secondary">What you are reading is the <strong>component output area</strong> &mdash; the main content rendered by Joomla\'s article system. Everything around it (the header links, navigation, sidebar, footer) is powered by modules assigned to Atomic template positions.</p>'
            . '<p class="text-body-secondary">To manage modules, go to <strong>Content &rarr; Site Modules</strong>. To configure the template itself, visit <strong>System &rarr; Site Templates &rarr; Atomic</strong>.</p>'
            . '</div>';

        $data = [
            'id'         => 0,
            'title'      => 'Welcome to Atomic',
            'alias'      => 'welcome-to-atomic',
            'catid'      => $catid,
            'state'      => 1,
            'language'   => '*',
            'access'     => $access,
            'created_by' => (int) ($user->id ?? 0),
            'introtext'  => $introtext,
            'fulltext'   => $fulltext,
            'attribs'    => '{}',
            'metadata'   => '{}',
            'images'     => '{}',
            'urls'       => '{}',
            'featured'   => 1,
            'featured_up' => null,
        ];

        if (!$articleModel->save($data)) {
            throw new \RuntimeException($articleModel->getError());
        }

        // Set ordering to 1 (top priority) in the featured content table
        $db = $this->getDatabase();
        $articleId = (int) $articleModel->getState('article.id');
        if ($articleId) {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__content_frontpage'))
                ->set($db->quoteName('ordering') . ' = 1')
                ->where($db->quoteName('content_id') . ' = :id')
                ->bind(':id', $articleId, ParameterType::INTEGER);
            $db->setQuery($query);
            $db->execute();
        }
    }

    private function createGettingStartedArticle(int $catid): void
    {
        /** @var \Joomla\Component\Content\Administrator\Model\ArticleModel $articleModel */
        $articleModel = $this->getApplication()->bootComponent('com_content')->getMVCFactory()
            ->createModel('Article', 'Administrator', ['ignore_request' => true]);

        $user   = $this->getApplication()->getIdentity();
        $access = (int) $this->getApplication()->get('access', 1);

        $introtext = '<p class="lead">A practical checklist to configure Atomic and get your site looking great in minutes.</p>';

        $fulltext = '<div class="list-group mb-4">'
            . '<div class="list-group-item">'
            . '<div class="d-flex align-items-start gap-3">'
            . '<span class="badge text-bg-primary rounded-pill mt-1">1</span>'
            . '<div>'
            . '<div class="fw-semibold">Set Bootstrap source</div>'
            . '<div class="small text-body-secondary">Choose Joomla-provided, CDN, a Bootswatch theme, or your own custom build in the <strong>Joomla</strong> tab.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="list-group-item">'
            . '<div class="d-flex align-items-start gap-3">'
            . '<span class="badge text-bg-primary rounded-pill mt-1">2</span>'
            . '<div>'
            . '<div class="fw-semibold">Choose fonts and CSS custom properties</div>'
            . '<div class="small text-body-secondary">Pick Google Fonts for headings and body in the <strong>Typography</strong> tab. Atomic sets <code>--atomic-header-font</code> and <code>--atomic-body-font</code> automatically.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="list-group-item">'
            . '<div class="d-flex align-items-start gap-3">'
            . '<span class="badge text-bg-primary rounded-pill mt-1">3</span>'
            . '<div>'
            . '<div class="fw-semibold">Configure layout columns and sticky header</div>'
            . '<div class="small text-body-secondary">Set header columns, content column widths, and enable the sticky header in the <strong>Layout</strong> tab.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="list-group-item">'
            . '<div class="d-flex align-items-start gap-3">'
            . '<span class="badge text-bg-primary rounded-pill mt-1">4</span>'
            . '<div>'
            . '<div class="fw-semibold">Set up theme switcher: light, dark, or auto</div>'
            . '<div class="small text-body-secondary">Enable the built-in light/dark mode toggle in the <strong>Features</strong> tab. Atomic respects <code>prefers-color-scheme</code> by default.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="list-group-item">'
            . '<div class="d-flex align-items-start gap-3">'
            . '<span class="badge text-bg-primary rounded-pill mt-1">5</span>'
            . '<div>'
            . '<div class="fw-semibold">Assign modules to positions</div>'
            . '<div class="small text-body-secondary">Go to <strong>Content &rarr; Site Modules</strong> and assign modules to Atomic positions like <code>hero</code>, <code>leftbody</code>, <code>rightbody</code>, and <code>footer</code>.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="list-group-item">'
            . '<div class="d-flex align-items-start gap-3">'
            . '<span class="badge text-bg-primary rounded-pill mt-1">6</span>'
            . '<div>'
            . '<div class="fw-semibold">Add social meta</div>'
            . '<div class="small text-body-secondary">Set Open Graph and Twitter Card defaults in the <strong>Meta</strong> tab so shared links look great on social platforms.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="alert alert-info" role="alert">'
            . '<i class="fa-solid fa-circle-info me-1" aria-hidden="true"></i> '
            . '<strong>Tip:</strong> Atomic supports all Cassiopeia module positions too. If you are migrating from Cassiopeia, your existing module assignments will continue to work.'
            . '</div>';

        $data = [
            'id'         => 0,
            'title'      => 'Getting Started with Atomic',
            'alias'      => 'getting-started',
            'catid'      => $catid,
            'state'      => 1,
            'language'   => '*',
            'access'     => $access,
            'created_by' => (int) ($user->id ?? 0),
            'introtext'  => $introtext,
            'fulltext'   => $fulltext,
            'attribs'    => '{}',
            'metadata'   => '{}',
            'images'     => '{}',
            'urls'       => '{}',
            'featured'   => 1,
        ];

        if (!$articleModel->save($data)) {
            throw new \RuntimeException($articleModel->getError());
        }
    }

    private function createFeaturesArticle(int $catid): void
    {
        /** @var \Joomla\Component\Content\Administrator\Model\ArticleModel $articleModel */
        $articleModel = $this->getApplication()->bootComponent('com_content')->getMVCFactory()
            ->createModel('Article', 'Administrator', ['ignore_request' => true]);

        $user   = $this->getApplication()->getIdentity();
        $access = (int) $this->getApplication()->get('access', 1);

        $introtext = '<p class="lead">Atomic packs a wide range of options into a lightweight template. Explore every settings tab and all the template positions available to you.</p>';

        $fulltext = '<h3 class="h4 fw-bold mb-3">Settings Overview</h3>'
            . '<div class="table-responsive mb-5">'
            . '<table class="table table-striped table-hover align-middle">'
            . '<thead class="table-light">'
            . '<tr>'
            . '<th style="width:25%">Settings Tab</th>'
            . '<th>Key Features</th>'
            . '</tr>'
            . '</thead>'
            . '<tbody>'
            . '<tr>'
            . '<td class="fw-semibold">Joomla</td>'
            . '<td>Bootstrap source (Joomla/CDN/Bootswatch/Custom), FontAwesome version, jQuery toggle</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">CSS</td>'
            . '<td>Custom CSS file loading, CSS custom properties, template-level overrides</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">Typography</td>'
            . '<td>Google Fonts for headings and body, font size, line height, font weight</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">Layout</td>'
            . '<td>Header columns, content column widths, sticky header, fluid/boxed container</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">Colors</td>'
            . '<td>Header/footer background colors, menu alias as body class, body class/ID options</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">Features</td>'
            . '<td>Light/dark theme switcher, back-to-top button, Google Analytics/Tag Manager</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">Meta</td>'
            . '<td>Open Graph tags, Twitter Cards, social sharing image, favicon</td>'
            . '</tr>'
            . '<tr>'
            . '<td class="fw-semibold">Code</td>'
            . '<td>Custom code injection: head, before body close, component override</td>'
            . '</tr>'
            . '</tbody>'
            . '</table>'
            . '</div>'
            . '<h3 class="h4 fw-bold mb-3">Template Positions at a Glance</h3>'
            . '<div class="row g-3">'
            . '<div class="col-md-6 col-lg-4">'
            . '<div class="card h-100 border">'
            . '<div class="card-body">'
            . '<h4 class="h6 card-title fw-bold">Header Area</h4>'
            . '<p class="card-text small text-body-secondary mb-0"><code>alert</code> &middot; <code>header</code> &middot; <code>header-center</code> &middot; <code>header-right</code></p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-6 col-lg-4">'
            . '<div class="card h-100 border">'
            . '<div class="card-body">'
            . '<h4 class="h6 card-title fw-bold">Navigation</h4>'
            . '<p class="card-text small text-body-secondary mb-0"><code>topmenu</code> &middot; <code>menu</code> &middot; <code>mobilemenu</code> &middot; <code>sidebar-menu</code> &middot; <code>breadcrumbs</code></p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-6 col-lg-4">'
            . '<div class="card h-100 border">'
            . '<div class="card-body">'
            . '<h4 class="h6 card-title fw-bold">Content Area</h4>'
            . '<p class="card-text small text-body-secondary mb-0"><code>hero</code> &middot; <code>abovebody</code> &middot; <code>leftbody</code> &middot; <code>rightbody</code> &middot; <code>belowbody</code></p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-6 col-lg-4">'
            . '<div class="card h-100 border">'
            . '<div class="card-body">'
            . '<h4 class="h6 card-title fw-bold">Extended Areas</h4>'
            . '<p class="card-text small text-body-secondary mb-0"><code>main-top</code> &middot; <code>main-bottom</code></p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-6 col-lg-4">'
            . '<div class="card h-100 border">'
            . '<div class="card-body">'
            . '<h4 class="h6 card-title fw-bold">Footer</h4>'
            . '<p class="card-text small text-body-secondary mb-0"><code>footer</code> &middot; <code>footer-center</code> &middot; <code>footer-right</code></p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-6 col-lg-4">'
            . '<div class="card h-100 border">'
            . '<div class="card-body">'
            . '<h4 class="h6 card-title fw-bold">Special</h4>'
            . '<p class="card-text small text-body-secondary mb-0"><code>debug</code> &middot; <code>error-403</code> &middot; <code>error-404</code></p>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';

        $data = [
            'id'         => 0,
            'title'      => 'Explore Atomic Features',
            'alias'      => 'explore-atomic-features',
            'catid'      => $catid,
            'state'      => 1,
            'language'   => '*',
            'access'     => $access,
            'created_by' => (int) ($user->id ?? 0),
            'introtext'  => $introtext,
            'fulltext'   => $fulltext,
            'attribs'    => '{}',
            'metadata'   => '{}',
            'images'     => '{}',
            'urls'       => '{}',
            'featured'   => 1,
        ];

        if (!$articleModel->save($data)) {
            throw new \RuntimeException($articleModel->getError());
        }
    }

    private function createStyleGuideArticle(int $catid): void
    {
        /** @var \Joomla\Component\Content\Administrator\Model\ArticleModel $articleModel */
        $articleModel = $this->getApplication()->bootComponent('com_content')->getMVCFactory()
            ->createModel('Article', 'Administrator', ['ignore_request' => true]);

        $user   = $this->getApplication()->getIdentity();
        $access = (int) $this->getApplication()->get('access', 1);

        $introtext = '<p class="lead">A visual reference of Bootstrap elements as rendered by your current theme. Use this page to preview how your Bootswatch or custom Bootstrap build will look.</p>';

        // Build the fulltext in sections for readability
        $fulltext = '';

        // --- Typography ---
        $fulltext .= '<h3 class="fw-bold mb-3">Typography</h3>'
            . '<h1>Heading 1</h1>'
            . '<h2>Heading 2</h2>'
            . '<h3>Heading 3</h3>'
            . '<h4>Heading 4</h4>'
            . '<h5>Heading 5</h5>'
            . '<h6>Heading 6</h6>'
            . '<p>This is a standard paragraph. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus luctus urna sed urna ultricies ac tempor dui sagittis.</p>'
            . '<p class="lead">This is a lead paragraph. It stands out from regular paragraphs.</p>'
            . '<blockquote class="blockquote">'
            . '<p>This is a blockquote. It is commonly used to highlight a quotation or key statement.</p>'
            . '</blockquote>'
            . '<p>Use <code>inline code</code> for short references, <mark>mark for highlights</mark>, <small>small for fine print</small>, and <strong>strong for emphasis</strong>.</p>'
            . '<hr class="my-4">';

        // --- Colors ---
        $fulltext .= '<h3 class="fw-bold mb-3">Colors</h3>'
            . '<div class="d-flex flex-wrap gap-2 mb-4">'
            . '<span class="badge text-bg-primary p-2">Primary</span>'
            . '<span class="badge text-bg-secondary p-2">Secondary</span>'
            . '<span class="badge text-bg-success p-2">Success</span>'
            . '<span class="badge text-bg-danger p-2">Danger</span>'
            . '<span class="badge text-bg-warning p-2">Warning</span>'
            . '<span class="badge text-bg-info p-2">Info</span>'
            . '<span class="badge text-bg-light p-2">Light</span>'
            . '<span class="badge text-bg-dark p-2">Dark</span>'
            . '</div>'
            . '<hr class="my-4">';

        // --- Buttons ---
        $fulltext .= '<h3 class="fw-bold mb-3">Buttons</h3>'
            . '<div class="d-flex flex-wrap gap-2 mb-3">'
            . '<button type="button" class="btn btn-primary">Primary</button>'
            . '<button type="button" class="btn btn-secondary">Secondary</button>'
            . '<button type="button" class="btn btn-success">Success</button>'
            . '<button type="button" class="btn btn-danger">Danger</button>'
            . '<button type="button" class="btn btn-warning">Warning</button>'
            . '<button type="button" class="btn btn-info">Info</button>'
            . '<button type="button" class="btn btn-light">Light</button>'
            . '<button type="button" class="btn btn-dark">Dark</button>'
            . '</div>'
            . '<div class="d-flex flex-wrap gap-2 mb-3">'
            . '<button type="button" class="btn btn-outline-primary">Primary</button>'
            . '<button type="button" class="btn btn-outline-secondary">Secondary</button>'
            . '<button type="button" class="btn btn-outline-success">Success</button>'
            . '<button type="button" class="btn btn-outline-danger">Danger</button>'
            . '<button type="button" class="btn btn-outline-warning">Warning</button>'
            . '<button type="button" class="btn btn-outline-info">Info</button>'
            . '<button type="button" class="btn btn-outline-light">Light</button>'
            . '<button type="button" class="btn btn-outline-dark">Dark</button>'
            . '</div>'
            . '<div class="d-flex flex-wrap gap-2 mb-4">'
            . '<button type="button" class="btn btn-primary btn-lg">Large</button>'
            . '<button type="button" class="btn btn-primary">Default</button>'
            . '<button type="button" class="btn btn-primary btn-sm">Small</button>'
            . '</div>'
            . '<hr class="my-4">';

        // --- Alerts ---
        $fulltext .= '<h3 class="fw-bold mb-3">Alerts</h3>'
            . '<div class="alert alert-success" role="alert"><strong>Success!</strong> This is a success alert with a brief message.</div>'
            . '<div class="alert alert-danger" role="alert"><strong>Error!</strong> This is a danger alert indicating something went wrong.</div>'
            . '<div class="alert alert-warning" role="alert"><strong>Warning!</strong> This is a warning alert for cautionary information.</div>'
            . '<div class="alert alert-info" role="alert"><strong>Info:</strong> This is an informational alert with helpful details.</div>'
            . '<hr class="my-4">';

        // --- Cards ---
        $fulltext .= '<h3 class="fw-bold mb-3">Cards</h3>'
            . '<div class="row g-3 mb-4">'
            . '<div class="col-md-4">'
            . '<div class="card h-100">'
            . '<div class="card-body">'
            . '<h5 class="card-title">Card Title One</h5>'
            . '<p class="card-text">Some example text to build on the card title and fill out the card content.</p>'
            . '<a href="#" class="btn btn-primary btn-sm">Go somewhere</a>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-4">'
            . '<div class="card h-100">'
            . '<div class="card-body">'
            . '<h5 class="card-title">Card Title Two</h5>'
            . '<p class="card-text">Another card with supporting text below as a natural lead-in to additional content.</p>'
            . '<a href="#" class="btn btn-primary btn-sm">Go somewhere</a>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-4">'
            . '<div class="card h-100">'
            . '<div class="card-body">'
            . '<h5 class="card-title">Card Title Three</h5>'
            . '<p class="card-text">A third card with brief text to demonstrate the card component layout.</p>'
            . '<a href="#" class="btn btn-primary btn-sm">Go somewhere</a>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<hr class="my-4">';

        // --- Table ---
        $fulltext .= '<h3 class="fw-bold mb-3">Table</h3>'
            . '<div class="table-responsive mb-4">'
            . '<table class="table table-striped table-hover">'
            . '<thead class="table-light">'
            . '<tr><th>#</th><th>Name</th><th>Position</th><th>Status</th></tr>'
            . '</thead>'
            . '<tbody>'
            . '<tr><td>1</td><td>Alice Johnson</td><td>Developer</td><td><span class="badge text-bg-success">Active</span></td></tr>'
            . '<tr><td>2</td><td>Bob Smith</td><td>Designer</td><td><span class="badge text-bg-success">Active</span></td></tr>'
            . '<tr><td>3</td><td>Carol White</td><td>Manager</td><td><span class="badge text-bg-warning">Away</span></td></tr>'
            . '<tr><td>4</td><td>David Brown</td><td>Analyst</td><td><span class="badge text-bg-danger">Inactive</span></td></tr>'
            . '<tr><td>5</td><td>Eve Davis</td><td>Architect</td><td><span class="badge text-bg-success">Active</span></td></tr>'
            . '</tbody>'
            . '</table>'
            . '</div>'
            . '<hr class="my-4">';

        // --- Forms ---
        $fulltext .= '<h3 class="fw-bold mb-3">Forms</h3>'
            . '<form class="mb-4">'
            . '<div class="mb-3">'
            . '<label for="sg-name" class="form-label">Name</label>'
            . '<input type="text" class="form-control" id="sg-name" placeholder="Enter your name">'
            . '</div>'
            . '<div class="mb-3">'
            . '<label for="sg-email" class="form-label">Email address</label>'
            . '<input type="email" class="form-control" id="sg-email" placeholder="name@example.com">'
            . '</div>'
            . '<div class="mb-3">'
            . '<label for="sg-select" class="form-label">Select an option</label>'
            . '<select class="form-select" id="sg-select">'
            . '<option selected>Choose...</option>'
            . '<option value="1">Option One</option>'
            . '<option value="2">Option Two</option>'
            . '<option value="3">Option Three</option>'
            . '</select>'
            . '</div>'
            . '<div class="mb-3">'
            . '<label for="sg-textarea" class="form-label">Message</label>'
            . '<textarea class="form-control" id="sg-textarea" rows="3" placeholder="Write your message here"></textarea>'
            . '</div>'
            . '<div class="mb-3 form-check">'
            . '<input type="checkbox" class="form-check-input" id="sg-check">'
            . '<label class="form-check-label" for="sg-check">Check me out</label>'
            . '</div>'
            . '<div class="mb-3 form-check form-switch">'
            . '<input class="form-check-input" type="checkbox" role="switch" id="sg-switch">'
            . '<label class="form-check-label" for="sg-switch">Toggle this switch</label>'
            . '</div>'
            . '<div class="mb-3">'
            . '<div class="form-check">'
            . '<input class="form-check-input" type="radio" name="sg-radio" id="sg-radio1" checked>'
            . '<label class="form-check-label" for="sg-radio1">Radio option one</label>'
            . '</div>'
            . '<div class="form-check">'
            . '<input class="form-check-input" type="radio" name="sg-radio" id="sg-radio2">'
            . '<label class="form-check-label" for="sg-radio2">Radio option two</label>'
            . '</div>'
            . '</div>'
            . '</form>'
            . '<hr class="my-4">';

        // --- List Groups ---
        $fulltext .= '<h3 class="fw-bold mb-3">List Groups</h3>'
            . '<div class="row g-3 mb-4">'
            . '<div class="col-md-6">'
            . '<h6 class="text-body-secondary mb-2">Standard</h6>'
            . '<ul class="list-group">'
            . '<li class="list-group-item">First item</li>'
            . '<li class="list-group-item">Second item</li>'
            . '<li class="list-group-item">Third item</li>'
            . '<li class="list-group-item">Fourth item</li>'
            . '</ul>'
            . '</div>'
            . '<div class="col-md-6">'
            . '<h6 class="text-body-secondary mb-2">Flush</h6>'
            . '<ul class="list-group list-group-flush">'
            . '<li class="list-group-item">First item</li>'
            . '<li class="list-group-item">Second item</li>'
            . '<li class="list-group-item">Third item</li>'
            . '<li class="list-group-item">Fourth item</li>'
            . '</ul>'
            . '</div>'
            . '</div>'
            . '<hr class="my-4">';

        // --- Accordion ---
        $fulltext .= '<h3 class="fw-bold mb-3">Accordion</h3>'
            . '<div class="accordion mb-4" id="sg-accordion">'
            . '<div class="accordion-item">'
            . '<h2 class="accordion-header">'
            . '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sg-acc1" aria-expanded="true" aria-controls="sg-acc1">What is Atomic?</button>'
            . '</h2>'
            . '<div id="sg-acc1" class="accordion-collapse collapse show" data-bs-parent="#sg-accordion">'
            . '<div class="accordion-body">Atomic is a minimal, admin-friendly Joomla template with 50+ settings, Google Fonts, Bootswatch themes, and flexible layouts built on Bootstrap 5.3.</div>'
            . '</div>'
            . '</div>'
            . '<div class="accordion-item">'
            . '<h2 class="accordion-header">'
            . '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sg-acc2" aria-expanded="false" aria-controls="sg-acc2">How do I change themes?</button>'
            . '</h2>'
            . '<div id="sg-acc2" class="accordion-collapse collapse" data-bs-parent="#sg-accordion">'
            . '<div class="accordion-body">Go to System &rarr; Site Templates &rarr; Atomic and select a Bootswatch theme from the Bootstrap Source dropdown in the Joomla tab. Save and your entire site updates instantly.</div>'
            . '</div>'
            . '</div>'
            . '<div class="accordion-item">'
            . '<h2 class="accordion-header">'
            . '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sg-acc3" aria-expanded="false" aria-controls="sg-acc3">Can I use custom fonts?</button>'
            . '</h2>'
            . '<div id="sg-acc3" class="accordion-collapse collapse" data-bs-parent="#sg-accordion">'
            . '<div class="accordion-body">Yes. Choose any Google Font from the Typography tab for headings and body text. Atomic loads them automatically and sets CSS custom properties you can use in your own stylesheets.</div>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<hr class="my-4">';

        // --- Badges & Progress ---
        $fulltext .= '<h3 class="fw-bold mb-3">Badges &amp; Progress</h3>'
            . '<div class="d-flex flex-wrap gap-2 mb-3">'
            . '<span class="badge text-bg-primary">Primary</span>'
            . '<span class="badge text-bg-secondary">Secondary</span>'
            . '<span class="badge text-bg-success">Success</span>'
            . '<span class="badge text-bg-danger">Danger</span>'
            . '<span class="badge text-bg-warning">Warning</span>'
            . '<span class="badge text-bg-info">Info</span>'
            . '<span class="badge text-bg-light">Light</span>'
            . '<span class="badge text-bg-dark">Dark</span>'
            . '</div>'
            . '<div class="mb-2"><div class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: 25%">25%</div></div></div>'
            . '<div class="mb-2"><div class="progress" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-success" style="width: 50%">50%</div></div></div>'
            . '<div class="mb-2"><div class="progress" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-warning" style="width: 75%">75%</div></div></div>'
            . '<div class="mb-4"><div class="progress" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-danger" style="width: 100%">100%</div></div></div>'
            . '<hr class="my-4">';

        // --- Pagination ---
        $fulltext .= '<h3 class="fw-bold mb-3">Pagination</h3>'
            . '<nav aria-label="Example pagination">'
            . '<ul class="pagination mb-4">'
            . '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>'
            . '<li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>'
            . '<li class="page-item"><a class="page-link" href="#">2</a></li>'
            . '<li class="page-item"><a class="page-link" href="#">3</a></li>'
            . '<li class="page-item"><a class="page-link" href="#">Next</a></li>'
            . '</ul>'
            . '</nav>'
            . '<hr class="my-4">';

        // --- Breadcrumb ---
        $fulltext .= '<h3 class="fw-bold mb-3">Breadcrumb</h3>'
            . '<nav aria-label="Example breadcrumb">'
            . '<ol class="breadcrumb">'
            . '<li class="breadcrumb-item"><a href="#">Home</a></li>'
            . '<li class="breadcrumb-item"><a href="#">Features</a></li>'
            . '<li class="breadcrumb-item active" aria-current="page">Style Guide</li>'
            . '</ol>'
            . '</nav>';

        $data = [
            'id'         => 0,
            'title'      => 'Style Guide',
            'alias'      => 'style-guide',
            'catid'      => $catid,
            'state'      => 1,
            'language'   => '*',
            'access'     => $access,
            'created_by' => (int) ($user->id ?? 0),
            'introtext'  => $introtext,
            'fulltext'   => $fulltext,
            'attribs'    => '{}',
            'metadata'   => '{}',
            'images'     => '{}',
            'urls'       => '{}',
            'featured'   => 0,
        ];

        if (!$articleModel->save($data)) {
            throw new \RuntimeException($articleModel->getError());
        }
    }

    // ------------------------------------------------------------------
    // Enhanced welcome article (Step 6)
    // ------------------------------------------------------------------

    private function ensureEnhancedWelcomeArticle(): void
    {
        $id = $this->getArticleIdByAlias('welcome-to-atomic');
        if (!$id) {
            // If missing, Step 1 will recreate it on next run; don't fail here.
            return;
        }

        /** @var \Joomla\Component\Content\Administrator\Model\ArticleModel $articleModel */
        $articleModel = $this->getApplication()->bootComponent('com_content')->getMVCFactory()
            ->createModel('Article', 'Administrator', ['ignore_request' => true]);

        // Load existing item
        $item = $articleModel->getItem($id);
        if (!$item) {
            return;
        }

        // Avoid overwriting if user has already edited heavily.
        $marker = '<!-- ATOMIC_SAMPLE_COMPONENT_START -->';
        if (is_string($item->fulltext) && strpos($item->fulltext, $marker) !== false) {
            return;
        }

        $full = $marker . '
<div class="mb-5">
  <h3 class="h4 fw-bold mb-3">Everything You Need</h3>
  <p class="text-body-secondary mb-4">Atomic gives you a complete set of tools to build any Joomla site. Here is what is included:</p>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card h-100 border">
        <div class="card-body text-center">
          <div class="h3 text-primary mb-2"><i class="fa-solid fa-cubes"></i></div>
          <h4 class="h6 fw-bold">Bootstrap Source</h4>
          <p class="small text-body-secondary mb-0">Joomla-provided, CDN, Bootswatch themes, or your own custom build.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 border">
        <div class="card-body text-center">
          <div class="h3 text-primary mb-2"><i class="fa-solid fa-circle-half-stroke"></i></div>
          <h4 class="h6 fw-bold">Theme Switcher</h4>
          <p class="small text-body-secondary mb-0">Built-in light/dark/auto mode toggle that respects user preference.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 border">
        <div class="card-body text-center">
          <div class="h3 text-primary mb-2"><i class="fa-solid fa-font"></i></div>
          <h4 class="h6 fw-bold">Google Fonts</h4>
          <p class="small text-body-secondary mb-0">Choose any Google Font for headings and body via CSS custom properties.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 border">
        <div class="card-body text-center">
          <div class="h3 text-primary mb-2"><i class="fa-solid fa-columns"></i></div>
          <h4 class="h6 fw-bold">Layout Config</h4>
          <p class="small text-body-secondary mb-0">Header columns, content column widths, sticky header, and fluid container.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 border">
        <div class="card-body text-center">
          <div class="h3 text-primary mb-2"><i class="fa-solid fa-share-nodes"></i></div>
          <h4 class="h6 fw-bold">Social Meta</h4>
          <p class="small text-body-secondary mb-0">Open Graph and Twitter Card tags configured from a single settings tab.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card h-100 border">
        <div class="card-body text-center">
          <div class="h3 text-primary mb-2"><i class="fa-solid fa-code"></i></div>
          <h4 class="h6 fw-bold">Custom Code</h4>
          <p class="small text-body-secondary mb-0">Inject code into head, before body close, or override the component area.</p>
        </div>
      </div>
    </div>
  </div>

  <h3 class="h4 fw-bold mb-3">Key Positions</h3>
  <div class="table-responsive border rounded-3">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Position</th>
          <th>Purpose</th>
          <th class="text-end">Typical Use</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><code>hero</code></td>
          <td class="text-body-secondary">Full-width area above the content columns</td>
          <td class="text-end"><span class="badge text-bg-primary-subtle border">Hero banner</span></td>
        </tr>
        <tr>
          <td><code>abovebody</code></td>
          <td class="text-body-secondary">Between hero and content columns</td>
          <td class="text-end"><span class="badge text-bg-success-subtle border">Featured content</span></td>
        </tr>
        <tr>
          <td><code>leftbody</code> / <code>rightbody</code></td>
          <td class="text-body-secondary">Sidebars flanking the main content</td>
          <td class="text-end"><span class="badge text-bg-info-subtle border">Sidebar widgets</span></td>
        </tr>
        <tr>
          <td><code>belowbody</code></td>
          <td class="text-body-secondary">Below the content columns</td>
          <td class="text-end"><span class="badge text-bg-secondary-subtle border">Call to action</span></td>
        </tr>
        <tr>
          <td><code>footer</code> / <code>footer-center</code> / <code>footer-right</code></td>
          <td class="text-body-secondary">Three-column footer area</td>
          <td class="text-end"><span class="badge text-bg-dark-subtle border">Site footer</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<!-- ATOMIC_SAMPLE_COMPONENT_END -->';

        $data = [
            'id'        => $id,
            'title'     => (string) $item->title,
            'alias'     => (string) $item->alias,
            'catid'     => (int) $item->catid,
            'state'     => (int) $item->state,
            'language'  => (string) $item->language,
            'access'    => (int) $item->access,
            'introtext' => (string) $item->introtext,
            'fulltext'  => (string) $item->fulltext . $full,
        ];

        if (!$articleModel->save($data)) {
            throw new \RuntimeException($articleModel->getError());
        }
    }

    // ------------------------------------------------------------------
    // Menu helpers
    // ------------------------------------------------------------------

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

    // ------------------------------------------------------------------
    // Tag helpers
    // ------------------------------------------------------------------

    /**
     * Create sample tags if they do not already exist and return alias => id map.
     */
    private function ensureSampleTags(): array
    {
        $tags = [
            'bootstrap'  => 'Bootstrap',
            'typography' => 'Typography',
            'layout'     => 'Layout',
            'themes'     => 'Themes',
            'fonts'      => 'Fonts',
        ];

        $tagIds = [];
        $db     = $this->getDatabase();

        foreach ($tags as $alias => $title) {
            $a = $alias;
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__tags'))
                ->where($db->quoteName('alias') . ' = :alias')
                ->bind(':alias', $a, ParameterType::STRING);
            $db->setQuery($query);
            $existing = (int) $db->loadResult();

            if ($existing) {
                $tagIds[$alias] = $existing;
                continue;
            }

            /** @var \Joomla\Component\Tags\Administrator\Model\TagModel $tagModel */
            $tagModel = $this->getApplication()->bootComponent('com_tags')->getMVCFactory()
                ->createModel('Tag', 'Administrator', ['ignore_request' => true]);

            $data = [
                'id'              => 0,
                'parent_id'       => 1,
                'title'           => $title,
                'alias'           => $alias,
                'published'       => 1,
                'language'        => '*',
                'access'          => (int) $this->getApplication()->get('access', 1),
                'params'          => '{}',
                'metadata'        => '{}',
                'metadesc'        => '',
                'metakey'         => '',
                'description'     => '',
            ];

            if (!$tagModel->save($data)) {
                throw new \RuntimeException($tagModel->getError());
            }

            $tagIds[$alias] = (int) $tagModel->getState('tag.id');
        }

        return $tagIds;
    }

    /**
     * Assign tags to an article by updating via the Article model.
     */
    private function assignTagsToArticle(int $articleId, array $tagIds): void
    {
        if (!$articleId || empty($tagIds)) {
            return;
        }

        /** @var \Joomla\Component\Content\Administrator\Model\ArticleModel $model */
        $model = $this->getApplication()->bootComponent('com_content')->getMVCFactory()
            ->createModel('Article', 'Administrator', ['ignore_request' => true]);

        $item = $model->getItem($articleId);

        if (!$item || !$item->id) {
            return;
        }

        $data = [
            'id'         => (int) $item->id,
            'title'      => $item->title,
            'alias'      => $item->alias,
            'catid'      => (int) $item->catid,
            'state'      => (int) $item->state,
            'language'   => $item->language,
            'access'     => (int) $item->access,
            'featured'   => (int) $item->featured,
            'introtext'  => $item->introtext,
            'fulltext'   => $item->fulltext,
            'tags'       => array_values($tagIds),
        ];

        $model->save($data);
    }
}
