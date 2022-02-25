<?php
/**
 * @copyright	Copyright (C) 2008-2022 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//	Get the application object for things like displaying the site name 
$app  = JFactory::getApplication();
$user = JFactory::getUser();

//	Output as HTML5
$this->setHtml5(true);

//	Get the alias of the current menu item
$active = JFactory::getApplication()->getMenu()->getActive();

//	Get params from template
$params = $app->getTemplate(true)->params;

//	Assign template params
$logo									= $this->params->get('logo');
$sitetitle								= $this->params->get('sitetitle');
$sitedescription					= $this->params->get('sitedescription');
$bodymenu							= $this->params->get('bodymenu');
$bootstrapsource				= $this->params->get('bootstrapsource');
$bootstrapcdn						= $this->params->get('bootstrapcdn');
$fontawesome						= $this->params->get('fontawesome');
$fontawesomecdn				= $this->params->get('fontawesomecdn');
$customcsscode					= $this->params->get('customcsscode');
$customcssfile					= $this->params->get('customcssfile');
$customjs							= $this->params->get('customjs');
$fluidcontainer						= $this->params->get('fluidcontainer');
$jqlibrary								= $this->params->get('jqlibrary');
$jquerycdn							= $this->params->get('jquerycdn');
$bsfixjoomla						= $this->params->get('bsfixjoomla');
$gacode								= $this->params->get('gacode');
$pageheader						= $this->params->get('pageheader');
$pageheadermod				= $this->params->get('pageheadermod');
$topmenu							= $this->params->get('topmenu');
$abovebody							= $this->params->get('abovebody');
$leftbody								= $this->params->get('leftbody');
$rightbody							= $this->params->get('rightbody');
$belowbody							= $this->params->get('belowbody');
$footer									= $this->params->get('footer');
$alertbar								= $this->params->get('alertbar');
$headerfont							= $this->params->get('headerfont');
$headerfontname				= $this->params->get('headerfontname');
$bodyfont							= $this->params->get('bodyfont');
$bodyfontname					= $this->params->get('bodyfontname');
$killjoomlajs							= $this->params->get('killjoomlajs');
$killjoomlacss						= $this->params->get('killjoomlacss');
$killgenerator						= $this->params->get('killgenerator');
$copyright							= $this->params->get('copyright');
$copyrighttxt						= $this->params->get('copyrighttxt');
$noconflict							= $this->params->get('noconflict');
$jqmigrate							= $this->params->get('jqmigrate');
$loadfavicons						= $this->params->get('loadfavicons');
$loadappleicons					= $this->params->get('loadappleicons');
$codeafterhead					= $this->params->get('codeafterhead');
$codebeforehead				= $this->params->get('codebeforehead');
$codeafterbody					= $this->params->get('codeafterbody');
$codebeforebody				= $this->params->get('codebeforebody');
$cssoverride						= $this->params->get('cssoverride');
$instant								= $this->params->get('instant');
$protopositions					= $this->params->get('protopositions');
$scrollreveal						= $this->params->get('scrollreveal');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<?php		//	 Add custom code after opening head tag
			if($codeafterhead != null) : ?>
			<?php echo $codeafterhead;
		?>	
		<?php endif; ?>
			
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1">
   	 	<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		
		<?php		//	 Output as HTML5
			$this->setHtml5(true);
		?>
		
		<?php		//	  Load jQuery 2 or 3 remotely	?>
		<?php if (($jquerycdn == null) && ($jqlibrary == 1)) : ?>
			<?php $doc = JFactory::getDocument();
							unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']); ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 2)) : ?>
			<?php $doc = JFactory::getDocument();
							unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']); ?>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<?php elseif (($jquerycdn == null) && ($jqlibrary == 3)) : ?>
			<?php $doc = JFactory::getDocument();
							unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']); ?>
		<?php else : ?>
			<script src="https://<?php echo $jquerycdn ?>"></script>
		<?php endif; ?>		
		
		<jdoc:include type="head" />
		
		<?php		//	 Remove Joomla head scripts (third-party scripts will still load)
			if($killjoomlajs == 1) : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/modals/js/script.min.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
					if (isset($this->_script['text/javascript'])) { 
						$this->_script['text/javascript'] = preg_replace('/jQuery\(window\).on\(\'load\'\,  function\(\) \{(.*);/is', '', $this->_script['text/javascript']);
						if (empty($this->_script['text/javascript'])) {
							unset($this->_script['text/javascript']);
						}
					}
			?>
		<?php endif; ?>
		
		<?php		//	 Use jQuery noConflict()
			if($noconflict == 0)  : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);
			?>
		<?php endif; ?>
		
		<?php		//	 Use jQuery Migrate
			if($jqmigrate >= 1) : ?>
				<?php $doc = JFactory::getDocument();
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
			?>
			<?php elseif($jqmigrate = 1) : ?>
				<script defer src="https://code.jquery.com/jquery-migrate-3.3.1.min.js" crossorigin="anonymous"></script>
		<?php endif; ?>
		
		<?php		//	 Use Scroll Reveal
			if($scrollreveal == 1) : ?>
			<script src="https://unpkg.com/scrollreveal"></script>
		<?php endif; ?>
		
		<?php 		//	 Remove Joomla CSS file (Modal styles)
			if($killjoomlacss == 1) : ?>
			<?php $doc = JFactory::getDocument();
				unset($this->_stylesheets[JURI::root(true) . '/media/modals/css/bootstrap.min.css']); 
				unset($this->_stylesheets[JURI::root(true) . '/media/jui/css/bootstrap.min.css']); 
				unset($this->_stylesheets[JURI::root(true) . '/media/jui/css/bootstrap-responsive.min.css']); 
				unset($this->_stylesheets[JURI::root(true) . '/media/jui/css/bootstrap-extended.css']); 
				?>
		<?php endif; ?>

		<?php 		//	 Remove Joomla generator tag
			if($killgenerator == 1) : ?>
			<?php $this->setGenerator(null); ?>
		<?php endif; ?>
   	 	   	 	
		<?php		//	 Load local or remote Bootstrap 4.6 CSS framework, or from CDN
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
				
				<?php 		//	 Load Bootswatch theme. Themes also include BS.
							elseif($bootstrapsource == 10) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/cerulean/bootstrap.min.css" integrity="sha512-CVn5BJ6vue0EG9CDO6yfg3hOvzQ43xEXOSUEkUtiV3pDy2S7O0saUZ0vbDkZYVX30NvqLLZo51JBzUyreGyqvg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 11) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/cosmo/bootstrap.min.css" integrity="sha512-kDz/z2objd/M3g7gA3zmFbKDsqUZQR1wgte/udIxu++yUeNg9l+VZJPjyxdXFZRA1DkXxdbWQPXMhJK4a8/v1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 12) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/flatly/bootstrap.min.css" integrity="sha512-YMq8wxbe3Zv+pHWC5Dqs5WTzd3BGyjloiVOHkqIqh2QObrUf03b1bjw1vnkFAABjuJO1KB89SKogBtJ81FOxpQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 13) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/journal/bootstrap.min.css" integrity="sha512-jt3EWRy/gs+PmzhhDm8quZeSrR/RaXSYeuYEo5bFz9W2uZ1hqh01VVn/dBvqEWgfNZaa0Zg7GJ1L92pTRbbHFg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 14) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/litera/bootstrap.min.css" integrity="sha512-hAjsunVAmC3XhLfgtEybEDJ/Ae3q06FEoqoc6NitOdGr5SUKrA+putqDvakQeWi2EEy0pW7ipri9oBjrBb7+zg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 15) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/lumen/bootstrap.min.css" integrity="sha512-iNpepALP/QVjr2YQZjyaBe4OTIhoWvAjaYckFcgq87c9s4h5Y3ZzB7AQJiOjcd4uNLj9MvOk5E8Dsh5SRn0zsg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 16) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/lux/bootstrap.min.css" integrity="sha512-B5sIrmt97CGoPUHgazLWO0fKVVbtXgGIOayWsbp9Z5aq4DJVATpOftE/sTTL27cu+QOqpI/jpt6tldZ4SwFDZw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 17) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/materia/bootstrap.min.css" integrity="sha512-3zLMbhWdUvhYuBzSXHe/ZiegRiepqocHcWdqYUssWTi2B6ATIlvpu2Eci+PiqWXhnNjVugl0VEaIXUfA1p/UZA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 18) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/minty/bootstrap.min.css" integrity="sha512-dzNHnGRA5Hl/Yg99wApuTJsmIZeGI982+8TIa+p4YY56wHgm99KoeG9/PctDhl9WikDBDpFhEoLBI+QZcNidmw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 19) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/morph/bootstrap.min.css" integrity="sha512-P74ekVMF6EUCnLTSVOkPjp3DWmXYmTHmr+Q3dMnVqH8e1O2z2vYtSEHEprV2Xsc7fT3wrLp7bUoI6nR/fwU7Gg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 20) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/pulse/bootstrap.min.css" integrity="sha512-kkiZw+Ko7F8zBSX53NnQ10YFQw+WREPjp8/ZS4PNYYfgdEOgnMIBlPs1gmKPHwaEU+Ig4Yf+WuD4/yd/4VUOwg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 21) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/quartz/bootstrap.min.css" integrity="sha512-k7BnoxK4uQKh1/VO7v9sOE/Kw7S6q4KnhDN/HpVqDXAaqUgBoD4Sir7ba/mcVGmT0p6MRi7KPGDaN53tPACnww==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 22) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/sandstone/bootstrap.min.css" integrity="sha512-mBH4X30KZkLSnDdvuiwV5mTHP17YpQsGyeLfHd7lV6SrkqlE11sMmhs9gBoV3YG/Lr0dhnoDPTwOPZOawrVByQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 23) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/simplex/bootstrap.min.css" integrity="sha512-MkI4WaJcuXvOVZPwGbYYSeq9qOPLI3dLVJhuf+vuaLgwLzC/PwLh1EzivMsoYn7Hl/VUQqPb4u4oTS2/XMJ4uQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 24) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/sketchy/bootstrap.min.css" integrity="sha512-v4QCPnzi0F9Jq2AWqOVTcPa+oFezd5XvAY9uV75H+h4KHNoKy9o1CspJJBb+O0Sw/OMzakuwEJWqTl8NOkM3eg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 25) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/spacelab/bootstrap.min.css" integrity="sha512-9FhFey1fi0I+oPa4VsNY37tritNhyim/IsvfayWEKjbqDrWVAN3qXph2kM8psvd+qQzrLc63d0evHRciplaRdw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 26) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/united/bootstrap.min.css" integrity="sha512-kF5MEZLjcx+Ct4XnVFWBTzYFBAX2y7ZN1p9LxZHEEr6HvkwuGURvdrg15AUZh+WwPOwWf53RbJNgN8jAcUMpxQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 27) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/vapor/bootstrap.min.css" integrity="sha512-Cigyxx5XP3ZOXrs2YTxOZjmj/s/i5ewSUXeaZv0ELqgRAb2SBS6gsMXix7xwAdjKcPkqzsP2huKF8QluqbgAFA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 28) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/yeti/bootstrap.min.css" integrity="sha512-DRLYyGEF8wn2r7FjKvYNc3sdoznkmLlMEb6ceVtekJNhE56Pp/qASznKvtsVvHXc3Lscy6hp2BgzfDMkCOlxkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 30) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/cyborg/bootstrap.min.css" integrity="sha512-/in5IWTUhb7wOUd6iHotlyrLrZ7+2utJJR8ySzSxeeOMJ9fanjCr4fmyWzDW/ziw56shUNTVClBMWZaA677VhA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 31) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/darkly/bootstrap.min.css" integrity="sha512-ZdxIsDOtKj2Xmr/av3D/uo1g15yxNFjkhrcfLooZV5fW0TT7aF7Z3wY1LOA16h0VgFLwteg14lWqlYUQK3to/w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 32) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/slate/bootstrap.min.css" integrity="sha512-lKxib8zP6dIxGCjD5AbC9emyFVlXedEg6pGLG08WfeLjcZjnKbHtSWL/+CFlQX8rB7AwEK8KbZUJFs3ip5cOtQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 33) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/solar/bootstrap.min.css" integrity="sha512-jJVf5SGg0vDRIZQUwozqfr5/JmboqwH9KVdcy5SWol5f5a0A3bDEYUTaq7Bycl5F7hxqqWJ2QaD26D11Yf6nFQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource ==34) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/superhero/bootstrap.min.css" integrity="sha512-A/CKosfzvYW9CWNoJfpmmNZZVobWO/DMtTU1RczyQnoVRhAgYNaLBRBtGKLFFyFmAyzyYofktg+xA4HDKK0t4g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
				<?php elseif($bootstrapsource == 35) : ?>
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.3/zephyr/bootstrap.min.css" integrity="sha512-gmJFuKTEQw6aHC2cWdrmdEHYw/M/8+bfsKmsJCcQ5hoI7WoOtkQ9hs7P0fqpBGRHHqBBJMibeCfhmk8So5qYUw==" crossorigin="anonymous" referrerpolicy="no-referrer" />				
				
			<?php elseif(($bootstrapcdn == null) && ($bootstrapsource == 2)) : ?>
				<?php echo $bootstrapcdn ?>
			<?php else : ?>
		<?php endif; ?>
		
		<?php		//	 Load the local CSS fixes for Joomla & Bootstrap 5.
			if($bsfixjoomla == 1) : ?>
				<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_bs5.css" type="text/css">
		<?php endif; ?>
		
		<?php 		//	 Load FontAwesome 5.14
			if($fontawesome == 1) : ?>
			<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/js/all.min.js"></script>
		<?php elseif($fontawesome == 2) : ?>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
		<?php elseif($fontawesome == 3) : ?>
			<link rel="stylesheet" href="<?php echo $fontawesomecdn; ?>">
		<?php elseif($fontawesome == 4) : ?>
			<script defer src="<?php echo $fontawesomecdn; ?>"></script>
		<?php endif; ?>
		
		<?php		//	  Load Google Fonts 	?>
		<?php if(($headerfont == 1) && ($bodyfont == 1) && ($headerfontname != null) && ($bodyfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $headerfontname; ?>|<?php echo $bodyfontname; ?>&display=swap">
			<style>h1,h2,h3,h4,h5,h6{font-family:'<?php echo $headerfontname; ?>',serif;}body{font-family:'<?php echo $bodyfontname; ?>',serif;}</style>
		<?php elseif(($headerfont == 1) && ($bodyfont == 0) && ($headerfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $headerfontname; ?>">
			<style>h1,h2,h3,h4,h5,h6{font-family:'<?php echo $headerfontname; ?>',serif;}</style>
		<?php elseif(($headerfont == 0) && ($bodyfont == 1) && ($bodyfontname != null)) : ?>
			<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo $bodyfontname; ?>">
			<style>body{font-family:'<?php echo $bodyfontname; ?>',serif;}</style>
		<?php else : ?>
		<?php endif; ?>		
		
		<?php 		//	 Load the RTL CSS file.
			if($this->direction == 'rtl') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template_rtl.css" type="text/css">
		<?php endif; ?>
		
		<?php		//	 Load the local CSS file for custom user CSS.
			if($customcssfile == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css">
		<?php endif; ?>
		
		<?php		//	 Load the local CSS file for custom user CSS. 
			if($cssoverride == 1) : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css">
		<?php endif; ?>
		
		<?php		//	 Add any custom user CSS from the configuration.
			if($customcsscode != null) : ?>
			<style>
				<?php echo $customcsscode ?>
			</style>
		<?php endif; ?>
		
		<?php		//	 Load template favicons, loaded by default
			if($loadfavicons == 1) : ?>
				<link rel="apple-touch-icon" sizes="180x180" href="/templates/<?php echo $this->template ?>/favicons/apple-touch-icon.png">
				<link rel="icon" type="image/png" sizes="32x32" href="/templates/<?php echo $this->template ?>/favicons/favicon-32x32.png">
				<link rel="icon" type="image/png" sizes="16x16" href="/templates/<?php echo $this->template ?>/favicons/favicon-16x16.png">
				<link rel="manifest" href="/templates/<?php echo $this->template ?>/favicons/site.webmanifest">
				<link rel="mask-icon" href="/templates/<?php echo $this->template ?>/favicons/safari-pinned-tab.svg" color="#5bbad5">
				<link rel="shortcut icon" href="/templates/<?php echo $this->template ?>/favicons/favicon.ico">
				<meta name="msapplication-TileColor" content="#da532c">
				<meta name="msapplication-config" content="/templates/<?php echo $this->template ?>/favicons/browserconfig.xml">
				<meta name="theme-color" content="#ffffff">
		<?php endif; ?>
		<?php		//	 Load Apple/Android precomposed icons
			if($loadappleicons == 1) : ?>
				<link rel="icon" sizes="192x192" href="touch-icon-192x192.png" ><!-- Android -->
				<link rel="apple-touch-icon-precomposed" sizes="180x180" href="<?php echo $this->baseurl ?>apple-touch-icon-180x180-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo $this->baseurl ?>apple-touch-icon-152x152-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $this->baseurl ?>apple-touch-icon-144x144-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo $this->baseurl ?>apple-touch-icon-120x120-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $this->baseurl ?>apple-touch-icon-114x114-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php echo $this->baseurl ?>apple-touch-icon-76x76-precomposed.png">
				<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $this->baseurl ?>apple-touch-icon-72x72-precomposed.png">
				<link rel="apple-touch-icon-precomposed" href="<?php echo $this->baseurl ?>apple-touch-icon-precomposed.png"><!-- 57×57px -->
		<?php endif; ?>
		
		<?php		//	 Load custom local user JavaScript
			if($customjs == 1) : ?>
			<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/template.js"></script>
		<?php endif; ?>
				
		<?php 		//	 Add Google Analytics tag if configured.
		if($gacode != null) : ?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gacode; ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
			gtag('config', '<?php echo $gacode; ?>');
		</script>
		<?php endif; ?>
		
		<?php		//	 Add custom code before closing head tag
			if($codebeforehead != null) : ?>
			<?php echo $codebeforehead;
		?>	
		<?php endif; ?>
		
	</head>
	
	<?php		//	 Add the menu item alias to the body ID, class, or both. Add protostar fixes. 	?>
	<?php if($bodymenu == 1) : ?>	
		<body class="<?php echo $active->alias; ?> <?php if($protopositions == 1) : ?>protostar<?php endif; ?> ">
	<?php elseif($bodymenu == 2) : ?>
		<body id="<?php echo $active->alias; ?>">
	<?php elseif($bodymenu == 3) : ?>
		<body id="<?php echo $active->alias; ?>" class="<?php echo $active->alias; ?> <?php if($protopositions == 1) : ?>protostar<?php endif; ?> ">
	<?php elseif($protopositions == 1) : ?>
		<body class="protostar">
	<?php else : ?>
		<body>
	<?php endif; ?>
	
	<?php		//	 Add custom code after opening body tag
		if($codeafterbody != null) : ?>
		<?php echo $codeafterbody;
	?>	
	<?php endif; ?>

	<?php 		//	 Choose either a fixed or fluid width container
		if($fluidcontainer == 1) : ?>
		<div class="container-fluid">
	<?php else : ?>
		<div class="container">
	<?php endif; ?>
	
	<?php		//	 Load the mobile menu position if a module published	
		if ($this->countModules( 'mobilemenu' )) : ?>
		<jdoc:include type="modules" name="mobilemenu" style="mobilemenu" />
	<?php endif; ?>

		<?php if(($pageheader == 1) || ($pageheadermod == 1)) : ?>	
			<div class="row">
				<div class="page-header">
				<?php if($pageheader == 1) : ?>
					<h1>
						<?php if ($logo) : ?>
							<span id="logo">
								<a href="<?php echo $this->baseurl; ?>"><img src="<?php echo $this->baseurl; ?>/<?php echo htmlspecialchars($logo); ?>" alt="<?php echo htmlspecialchars($this->params->get('sitetitle')); ?>" /></a>
							</span>
						<?php endif;?>
						<?php if($sitetitle != null) : ?><?php echo $sitetitle	; ?><?php endif; ?></h1>
						<?php if($sitedescription != null) : ?><p><small><?php echo $sitedescription; ?></small></p><?php endif; ?>
				<?php endif; ?>
				<?php if($pageheadermod == 1) : ?>
					<jdoc:include type="modules" name="pageheader" style="basic" />
				<?php endif; ?>
				</div>
				<?php if ($this->countModules('menu')) : ?>
				<div class="headermenu"><jdoc:include type="modules" name="menu"style="basic" /></div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		
			<?php if($topmenu == 1) : ?>
			<div class="row">
				<div class="col-md-9">
					<nav class="navigation">
						<div class="nav-collapse">
							<jdoc:include type="modules" name="navigation" style="basic" />
							<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-1" style="none" /><?php endif; ?>
						</div>
					</nav>
				</div>
				<div class="col-md-3">
					<jdoc:include type="modules" name="search" style="basic" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-0" style="none" /><?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

			<div class="row">
				<?php if(($leftbody == 1) && ($rightbody == 1)) : ?>	
				<div class="col-md-2 leftbody">
					<jdoc:include type="modules" name="leftbody"  style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-8" style="xhtml" /><?php endif; ?>
				</div>
				<div class="col-12 col-md-7 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody" style="xhtml" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
				<div class="col-md-3 rightbody">
					<jdoc:include type="modules" name="rightbody" style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-7" style="default" /><?php endif; ?>
				</div>
			
				<?php elseif(($leftbody == 0) && ($rightbody == 1)) : ?>
				<div class="col-md-9 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody"style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
				<div class="col-md-3 rightbody">
					<jdoc:include type="modules" name="rightbody" style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-7" style="default" /><?php endif; ?>
				</div>
		
				<?php elseif(($leftbody == 1) && ($rightbody == 0)) : ?>
				<div class="col-md-3 leftbody">
					<jdoc:include type="modules" name="leftbody" style="default" />
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-8" style="xhtml" /><?php endif; ?>
				</div>
				<div class="col-md-9 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody" style="xhtml" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
			
				<?php else : ?>
				<div class="col-md-12 mainbody">
					<jdoc:include type="message" />
					<?php if($abovebody == 1) : ?><jdoc:include type="modules" name="abovebody" style="xhtml" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-3" style="none" /><?php endif; ?>
					<jdoc:include type="component" />
					<?php if($belowbody == 1) : ?><jdoc:include type="modules" name="belowbody" style="default" /><?php endif; ?>
					<?php if($protopositions == 1) : ?><jdoc:include type="modules" name="position-2" style="none" /><?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		
			<?php if($footer == 1) : ?>
			<footer class="container">
				<div class="row">
					<jdoc:include type="modules" name="footer" style="none"style="basic" />
					<hr />
					<small>
						<?php if(($copyrighttxt != null) && ($copyright == 1)) : ?>
						&copy;<?php echo date('Y'); ?> <?php echo $copyrighttxt ?>
						<?php else : ?>
						&copy;<?php echo date('Y'); ?> <?php echo htmlspecialchars($app->getCfg('sitename')); ?>
						<?php endif; ?>
					</small>
				</div>
			</footer>
			<?php endif; ?>
			
			<?php if ($this->countModules( 'alertbar' )) : ?>
				<?php if($alertbar == 1) : ?>
				<div id="alertbar"><jdoc:include type="modules" name="alertbar" style="none" /></div>
				<?php endif; ?>
			<?php endif; ?>
		
			<jdoc:include type="modules" name="debug" style="basic" />
		</div>

		<?php		//	 Use jQuery Migrate 3.3.1
			if($jqmigrate == 1)  : ?>
				<script src="https://code.jquery.com/jquery-migrate-3.3.1.min.js" crossorigin="anonymous"></script>
		<?php endif; ?>
		
			<?php 		//	 Load the local or remote Bootstrap 3 or 4 JavaScript dependency
			 				//	 If CDN empty and load BS 3 remotely	
			if(($bootstrapcdn == null) && ($bootstrapsource == 1)) : ?>
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
			
			<?php 		//	 If CDN empty and load BS 5 remotely
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3)) : ?>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
			
			<?php 		//	 If CDN empty and load BS 5 remotely, but full jQuery 3 is loaded
				elseif(($bootstrapcdn == null) && ($bootstrapsource == 3) && ($jqlibrary == 2)) : ?>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
			
		<?php else : ?>
		<?php endif; ?>	
		
		<?php		//	 Add custom code before closing body tag
			if($codebeforebody != null) : ?>
			<?php echo $codebeforebody; ?>	
		<?php endif; ?>
		
		<?php		//	 Use Instant.page
			if($instant == 1) : ?>
			<script src="//instant.page/5.1.0" type="module" integrity="sha384-by67kQnR+pyfy8yWP4kPO12fHKRLHZPfEsiSXR8u2IKcTdxD805MGUXBzVPnkLHw"></script>
		<?php endif; ?>
		
	</body>
</html>
