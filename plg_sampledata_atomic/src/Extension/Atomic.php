<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kontent\Plugin\SampleData\Atomic\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Kontent\Plugin\SampleData\Atomic\Traits\ContentTrait;
use Kontent\Plugin\SampleData\Atomic\Traits\FinalizeTrait;
use Kontent\Plugin\SampleData\Atomic\Traits\MenuTrait;
use Kontent\Plugin\SampleData\Atomic\Traits\ModuleTrait;

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
    use ContentTrait;
    use DatabaseAwareTrait;
    use FinalizeTrait;
    use MenuTrait;
    use ModuleTrait;

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

        $db                 = $this->getDatabase();
        $transactionStarted = false;

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

            $db->transactionStart();
            $transactionStarted = true;

            // Nested-set category write first (Table\Nested → LOCK TABLES → implicit commit on MySQL):
            // a failure after the category commit rolls back the unflag, instead of the reverse.
            $catid = $this->ensureSampleCategory();

            // Unflag all currently-featured articles so Atomic content takes priority
            $this->unflagAllFeaturedArticles();

            $this->createWelcomeArticle($catid);

            $db->transactionCommit();
            $transactionStarted = false;

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP1_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $this->rollbackQuietly();
            }

            $this->logStepFailure(1, $e);
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

        $db                 = $this->getDatabase();
        $transactionStarted = false;

        try {
            if (!ComponentHelper::isEnabled('com_content') || !ComponentHelper::isEnabled('com_categories')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_CONTENT'),
                ];
            }

            $db->transactionStart();
            $transactionStarted = true;

            $catid = $this->ensureSampleCategory();

            if ($this->articleExists('getting-started')
                && $this->articleExists('explore-atomic-features')
                && $this->articleExists('style-guide')
            ) {
                // ensureSampleCategory() may have created the category — keep it
                $db->transactionCommit();
                $transactionStarted = false;

                $this->cleanBuffers($level);

                return [
                    'success' => true,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP2_ALREADY_INSTALLED'),
                ];
            }

            // Nested-set tag writes first (Table\Nested → LOCK TABLES → implicit commit on MySQL),
            // before the article inserts, so the implicit-commit point stays as early as possible.
            $tagIds = $this->ensureSampleTags();

            if (!$this->articleExists('getting-started')) {
                $this->createGettingStartedArticle($catid);
            }

            if (!$this->articleExists('explore-atomic-features')) {
                $this->createFeaturesArticle($catid);
            }

            if (!$this->articleExists('style-guide')) {
                $this->createStyleGuideArticle($catid);
            }

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

            $db->transactionCommit();
            $transactionStarted = false;

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP2_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $this->rollbackQuietly();
            }

            $this->logStepFailure(2, $e);
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

        $db                 = $this->getDatabase();
        $transactionStarted = false;

        try {
            if (!ComponentHelper::isEnabled('com_menus') || !ComponentHelper::isEnabled('com_content')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_MENUS'),
                ];
            }

            $db->transactionStart();
            $transactionStarted = true;

            // Nested-set category write first (Table\Nested → LOCK TABLES → implicit commit on MySQL),
            // before the plain #__menu_types insert, so the implicit-commit point stays as early as possible.
            $catid    = $this->ensureSampleCategory();
            $menuType = $this->ensureMenuType();

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

            $db->transactionCommit();
            $transactionStarted = false;

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP3_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $this->rollbackQuietly();
            }

            $this->logStepFailure(3, $e);
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

        $db                 = $this->getDatabase();
        $transactionStarted = false;

        try {
            if (!ComponentHelper::isEnabled('com_modules') || !ComponentHelper::isEnabled('com_menus')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_MODULES'),
                ];
            }

            $db->transactionStart();
            $transactionStarted = true;

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

            $db->transactionCommit();
            $transactionStarted = false;

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP4_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $this->rollbackQuietly();
            }

            $this->logStepFailure(4, $e);
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

        $db                 = $this->getDatabase();
        $transactionStarted = false;

        try {
            if (!ComponentHelper::isEnabled('com_modules') || !ComponentHelper::isEnabled('com_menus')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_NO_MODULES'),
                ];
            }

            $db->transactionStart();
            $transactionStarted = true;

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
            $this->createTagsModule('Atomic Tags', 'leftbody', 7, 1);

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

            // --- Login (rightbody) ---
            $this->createLoginModule('Atomic Login', 'rightbody', 10, 1);

            // --- Next Step (belowbody) ---
            $nextStepHtml = '<div class="glass p-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">'
                . '<div>'
                . '<div class="fw-semibold" style="color:var(--text-primary)">Ready to build?</div>'
                . '<div class="small" style="color:var(--text-secondary)">This is the <code>belowbody</code> position, below the main content area.</div>'
                . '</div>'
                . '<a class="btn btn-accent btn-sm" href="index.php?option=com_content&amp;view=article&amp;alias=getting-started">Getting Started Guide</a>'
                . '</div>';

            $this->createCustomModule('Atomic Next Step', 'belowbody', 11, 0, $nextStepHtml);

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

            $this->createCustomModule('Atomic Footer', 'footer', 12, 0, $footerHtml);

            // --- Footer Center ---
            $footerCenterHtml = '<h6>Resources</h6>'
                . '<ul>'
                . '<li><a href="#">Documentation</a></li>'
                . '<li><a href="#">Support</a></li>'
                . '<li><a href="#">Changelog</a></li>'
                . '<li><a href="#">GitHub</a></li>'
                . '</ul>';

            $this->createCustomModule('Atomic Footer Center', 'footer-center', 13, 0, $footerCenterHtml);

            // --- Footer Right ---
            $footerRightHtml = '<h6>About</h6>'
                . '<ul>'
                . '<li><a href="index.php?option=com_content&amp;view=article&amp;alias=explore-atomic-features">Features</a></li>'
                . '<li><a href="index.php?option=com_content&amp;view=article&amp;alias=style-guide">Style Guide</a></li>'
                . '<li><a href="index.php?option=com_content&amp;view=article&amp;alias=getting-started">Getting Started</a></li>'
                . '</ul>';

            $this->createCustomModule('Atomic Footer Right', 'footer-right', 14, 0, $footerRightHtml);

            $db->transactionCommit();
            $transactionStarted = false;

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP5_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $this->rollbackQuietly();
            }

            $this->logStepFailure(5, $e);
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

        $db                 = $this->getDatabase();
        $transactionStarted = false;

        try {
            if (!ComponentHelper::isEnabled('com_modules') || !ComponentHelper::isEnabled('com_content') || !ComponentHelper::isEnabled('com_menus')) {
                $this->cleanBuffers($level);

                return [
                    'success' => false,
                    'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_ERROR_STEP6_REQUIREMENTS'),
                ];
            }

            $db->transactionStart();
            $transactionStarted = true;

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

            // Apply recommended template settings
            $this->applyTemplateSettings();

            // Ensure correct featured article ordering
            $this->fixFeaturedArticleOrdering();

            // Hide page heading on the Home menu item
            $this->setHomeMenuPageHeading();

            $db->transactionCommit();
            $transactionStarted = false;

            $this->cleanBuffers($level);

            return [
                'success' => true,
                'message' => Text::_('PLG_SAMPLEDATA_ATOMIC_STEP6_SUCCESS'),
            ];
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $this->rollbackQuietly();
            }

            $this->logStepFailure(6, $e);
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
     * Roll back the current transaction without letting a rollback failure
     * mask the original error being reported to the sample data UI.
     */
    private function rollbackQuietly(): void
    {
        try {
            $this->getDatabase()->transactionRollback();
        } catch (\Throwable $rollbackError) {
            // Nothing left to do — the original exception is what gets reported
        }
    }

    /**
     * Record a step failure in the Joomla log so it leaves a persistent trace.
     */
    private function logStepFailure(int $step, \Throwable $e): void
    {
        try {
            Log::add(
                sprintf('Sample data step %d failed: %s', $step, $e->getMessage()),
                Log::ERROR,
                'plg_sampledata_atomic'
            );
        } catch (\Throwable $logError) {
            // Logging must never break the AJAX response
        }
    }
}
