<?php
/**
 * @copyright	Copyright (C) 2008-2026 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (!isset($this->error)) {
	$this->error = new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
	$this->debug = false;
}

$code        = $this->error->getCode();
$errorsearch = (int) $this->params->get('errorsearch', 1);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex">
		<title><?php echo $code; ?> - <?php echo $this->title; ?></title>
		<style>
			:root {
				--error-bg: #0b0c1a;
				--error-text: #e8eaf0;
				--error-muted: #7b8299;
				--error-accent: #ff6b35;
				--error-link: #6ea8fe;
			}
			* { box-sizing: border-box; margin: 0; padding: 0; }
			html, body {
				height: 100%;
				background-color: var(--error-bg);
				color: var(--error-text);
				font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
			}
			body {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				min-height: 100vh;
				padding: 2rem 1rem;
				text-align: center;
			}
			.error-icon {
				width: 80px;
				height: 80px;
				margin: 0 auto 1.5rem;
				color: var(--error-accent);
			}
			.error-code {
				font-size: clamp(5rem, 20vw, 9rem);
				font-weight: 900;
				letter-spacing: -0.04em;
				line-height: 1;
				color: var(--error-accent);
				margin-bottom: 0.5rem;
			}
			.error-title {
				font-size: clamp(1.25rem, 4vw, 1.75rem);
				font-weight: 600;
				margin-bottom: 1rem;
				color: var(--error-text);
			}
			.error-desc {
				font-size: 1rem;
				color: var(--error-muted);
				max-width: 480px;
				margin: 0 auto 2rem;
				line-height: 1.6;
			}
			.error-links {
				display: flex;
				flex-wrap: wrap;
				gap: 0.75rem;
				justify-content: center;
				margin-bottom: 2rem;
			}
			.error-links a {
				color: var(--error-link);
				text-decoration: none;
				padding: 0.5rem 1.25rem;
				border: 1px solid rgba(110, 168, 254, 0.35);
				border-radius: 0.5rem;
				font-size: 0.9rem;
				transition: background 0.15s, border-color 0.15s;
			}
			.error-links a:hover {
				background: rgba(110, 168, 254, 0.1);
				border-color: var(--error-link);
			}
			.error-tech {
				margin-top: 2rem;
				font-size: 0.8rem;
				color: var(--error-muted);
				opacity: 0.7;
			}
			/* Search form */
			.error-search {
				max-width: 420px;
				width: 100%;
				margin: 0 auto 2rem;
			}
			.error-search form {
				display: flex;
				gap: 0.5rem;
			}
			.error-search input[type="search"] {
				flex: 1;
				background: rgba(255,255,255,0.08);
				border: 1px solid rgba(255,255,255,0.15);
				border-radius: 0.5rem;
				color: var(--error-text);
				padding: 0.5rem 0.875rem;
				font-size: 0.9rem;
				outline: none;
			}
			.error-search input[type="search"]::placeholder { color: var(--error-muted); }
			.error-search input[type="search"]:focus { border-color: var(--error-link); }
			.error-search button {
				background: var(--error-accent);
				border: none;
				border-radius: 0.5rem;
				color: #fff;
				padding: 0.5rem 1rem;
				font-size: 0.9rem;
				cursor: pointer;
				white-space: nowrap;
			}
			.error-search button:hover { opacity: 0.88; }
			/* Stars background */
			.stars {
				position: fixed;
				inset: 0;
				z-index: -1;
				overflow: hidden;
			}
			.stars::before, .stars::after {
				content: '';
				position: absolute;
				inset: 0;
				background-image:
					radial-gradient(1px 1px at 20% 30%, rgba(255,255,255,0.6) 0%, transparent 100%),
					radial-gradient(1px 1px at 80% 10%, rgba(255,255,255,0.5) 0%, transparent 100%),
					radial-gradient(1.5px 1.5px at 50% 70%, rgba(255,255,255,0.4) 0%, transparent 100%),
					radial-gradient(1px 1px at 10% 90%, rgba(255,255,255,0.5) 0%, transparent 100%),
					radial-gradient(1px 1px at 90% 60%, rgba(255,255,255,0.4) 0%, transparent 100%),
					radial-gradient(1px 1px at 35% 15%, rgba(255,255,255,0.6) 0%, transparent 100%),
					radial-gradient(1px 1px at 65% 85%, rgba(255,255,255,0.5) 0%, transparent 100%),
					radial-gradient(1px 1px at 5% 45%, rgba(255,255,255,0.4) 0%, transparent 100%),
					radial-gradient(1px 1px at 75% 40%, rgba(255,255,255,0.6) 0%, transparent 100%),
					radial-gradient(1px 1px at 45% 55%, rgba(255,255,255,0.3) 0%, transparent 100%);
			}
			.stars::after {
				background-image:
					radial-gradient(1px 1px at 25% 65%, rgba(255,255,255,0.5) 0%, transparent 100%),
					radial-gradient(1px 1px at 70% 25%, rgba(255,255,255,0.4) 0%, transparent 100%),
					radial-gradient(1px 1px at 15% 75%, rgba(255,255,255,0.6) 0%, transparent 100%),
					radial-gradient(1px 1px at 85% 80%, rgba(255,255,255,0.5) 0%, transparent 100%),
					radial-gradient(1.5px 1.5px at 55% 35%, rgba(255,255,255,0.4) 0%, transparent 100%),
					radial-gradient(1px 1px at 40% 90%, rgba(255,255,255,0.5) 0%, transparent 100%),
					radial-gradient(1px 1px at 95% 50%, rgba(255,255,255,0.3) 0%, transparent 100%);
			}
		</style>
	</head>
	<body>
		<div class="stars" aria-hidden="true"></div>

		<svg class="error-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="rgba(255,107,53,0.12)"/>
			<line x1="12" y1="9" x2="12" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			<line x1="12" y1="17" x2="12.01" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
		</svg>

		<div class="error-code"><?php echo $code; ?></div>
		<h1 class="error-title"><?php echo $this->title; ?></h1>
		<p class="error-desc"><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>

		<?php if ($errorsearch) : ?>
		<div class="error-search">
			<form action="<?php echo $this->baseurl; ?>/index.php" method="get" role="search">
				<input type="hidden" name="option" value="com_finder">
				<input type="hidden" name="view" value="search">
				<input type="search" name="q" placeholder="Search the site&hellip;" aria-label="Search">
				<button type="submit">Search</button>
			</form>
		</div>
		<?php endif; ?>

		<nav class="error-links" aria-label="Navigation options">
			<a href="<?php echo $this->baseurl; ?>/index.php">Home</a>
			<a href="#" onclick="history.length > 1 ? history.back() : window.location.href='<?php echo $this->baseurl; ?>/index.php'; return false;">Go back</a>
		</nav>

		<div class="error-tech">
			ERROR <?php echo $code; ?>
			<?php if ($this->debug) : ?>
				<div style="margin-top:1rem; text-align:left; max-width:800px; background:rgba(255,255,255,0.05); padding:1rem; border-radius:0.5rem; font-family:monospace; font-size:0.75rem; overflow:auto;">
					<?php echo $this->renderBacktrace(); ?>
				</div>
			<?php endif; ?>
		</div>
	</body>
</html>
