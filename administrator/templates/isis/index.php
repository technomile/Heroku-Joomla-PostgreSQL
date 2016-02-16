<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.isis
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.0
 */

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$lang            = JFactory::getLanguage();
$this->language  = $doc->language;
$this->direction = $doc->direction;
$input           = $app->input;
$user            = JFactory::getUser();

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScriptVersion($this->baseurl . '/templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/template' . ($this->direction == 'rtl' ? '-rtl' : '') . '.css');

// Load custom.css
$file = 'templates/' . $this->template . '/css/custom.css';

if (is_file($file))
{
	$doc->addStyleSheetVersion($file);
}

// Load specific language related CSS
$file = 'language/' . $lang->getTag() . '/' . $lang->getTag() . '.css';

if (is_file($file))
{
	$doc->addStyleSheetVersion($file);
}

// Detecting Active Variables
$option   = $input->get('option', '');
$view     = $input->get('view', '');
$layout   = $input->get('layout', '');
$task     = $input->get('task', '');
$itemid   = $input->get('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename', ''), ENT_QUOTES, 'UTF-8');
$cpanel   = ($option === 'com_cpanel');

$hidden = JFactory::getApplication()->input->get('hidemainmenu');

$showSubmenu          = false;
$this->submenumodules = JModuleHelper::getModules('submenu');

foreach ($this->submenumodules as $submenumodule)
{
	$output = JModuleHelper::renderModule($submenumodule);

	if (strlen($output))
	{
		$showSubmenu = true;
		break;
	}
}

// Template Parameters
$displayHeader = $this->params->get('displayHeader', '1');
$statusFixed   = $this->params->get('statusFixed', '1');
$stickyToolbar = $this->params->get('stickyToolbar', '1');

// Header classes
$template_is_light = ($this->params->get('templateColor') && colorIsLight($this->params->get('templateColor')));
$header_is_light = ($displayHeader && $this->params->get('headerColor') && colorIsLight($this->params->get('headerColor')));

if ($displayHeader)
{
	// Logo file
	if ($this->params->get('logoFile'))
	{
		$logo = JUri::root() . $this->params->get('logoFile');
	}
	else
	{
		$logo = $this->baseurl . '/templates/' . $this->template . '/images/logo' . ($header_is_light ? '-inverse' : '') . '.png';
	}
}

function colorIsLight($color)
{
	$r = hexdec(substr($color, 1, 2));
	$g = hexdec(substr($color, 3, 2));
	$b = hexdec(substr($color, 5, 2));
	$yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

	return $yiq >= 200;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<jdoc:include type="head" />

	<!-- Template color -->
	<?php if ($this->params->get('templateColor')) : ?>
		<style type="text/css">
			.navbar-inner, .navbar-inverse .navbar-inner, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .navbar-inverse .nav li.dropdown.open > .dropdown-toggle, .navbar-inverse .nav li.dropdown.active > .dropdown-toggle, .navbar-inverse .nav li.dropdown.open.active > .dropdown-toggle, #status.status-top {
				background: <?php echo $this->params->get('templateColor'); ?>;
			}
		</style>
	<?php endif; ?>
	<!-- Template header color -->
	<?php if ($displayHeader && $this->params->get('headerColor')) : ?>
		<style type="text/css">
			.header {
				background: <?php echo $this->params->get('headerColor'); ?>;
			}
		</style>
	<?php endif; ?>

	<!-- Sidebar background color -->
	<?php if ($this->params->get('sidebarColor')) : ?>
		<style type="text/css">
			.nav-list > .active > a, .nav-list > .active > a:hover {
				background: <?php echo $this->params->get('sidebarColor'); ?>;
			}
		</style>
	<?php endif; ?>

	<!-- Link color -->
	<?php if ($this->params->get('linkColor')) : ?>
		<style type="text/css">
			a, .j-toggle-sidebar-button
			{
				color: <?php echo $this->params->get('linkColor'); ?>;
			}
		</style>
	<?php endif; ?>

	<!--[if lt IE 9]>
	<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<body class="admin <?php echo $option . ' view-' . $view . ' layout-' . $layout . ' task-' . $task . ' itemid-' . $itemid; ?>">
<!-- Top Navigation -->
<nav class="navbar<?php echo $template_is_light ? '' : ' navbar-inverse'; ?> navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<?php if ($this->params->get('admin_menus') != '0') : ?>
				<a href="#" class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
			<?php endif; ?>

			<a class="admin-logo <?php echo ($hidden ? 'disabled' : ''); ?>" <?php echo ($hidden ? '' : 'href="' . $this->baseurl . '"'); ?>><span class="icon-joomla"></span></a>

			<a class="brand hidden-desktop hidden-tablet" href="<?php echo JUri::root(); ?>" title="<?php echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename); ?>" target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
				<span class="icon-out-2 small"></span></a>

			<div<?php echo ($this->params->get('admin_menus') != '0') ? ' class="nav-collapse collapse"' : ''; ?>>
				<jdoc:include type="modules" name="menu" style="none" />
				<ul class="nav nav-user<?php echo ($this->direction == 'rtl') ? ' pull-left' : ' pull-right'; ?>">
					<li class="dropdown">
						<a class="<?php echo ($hidden ? ' disabled' : 'dropdown-toggle'); ?>" data-toggle="<?php echo ($hidden ? '' : 'dropdown'); ?>" <?php echo ($hidden ? '' : 'href="#"'); ?>><span class="icon-cog"></span>
							<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php if (!$hidden) : ?>
								<li>
									<span>
										<span class="icon-user"></span>
										<strong><?php echo $user->name; ?></strong>
									</span>
								</li>
								<li class="divider"></li>
								<li>
									<a href="index.php?option=com_admin&amp;task=profile.edit&amp;id=<?php echo $user->id; ?>"><?php echo JText::_('TPL_ISIS_EDIT_ACCOUNT'); ?></a>
								</li>
								<li class="divider"></li>
								<li class="">
									<a href="<?php echo JRoute::_('index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1'); ?>"><?php echo JText::_('TPL_ISIS_LOGOUT'); ?></a>
								</li>
							<?php endif; ?>
						</ul>
					</li>
				</ul>
				<a class="brand visible-desktop visible-tablet" href="<?php echo JUri::root(); ?>" title="<?php echo JText::sprintf('TPL_ISIS_PREVIEW', $sitename); ?>" target="_blank"><?php echo JHtml::_('string.truncate', $sitename, 14, false, false); ?>
					<span class="icon-out-2 small"></span></a>
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>
</nav>
<!-- Header -->
<?php if ($displayHeader) : ?>
	<header class="header<?php echo $header_is_light ? ' header-inverse' : ''; ?>">
		<div class="container-logo">
			<img src="<?php echo $logo; ?>" class="logo" alt="<?php echo $sitename;?>" />
		</div>
		<div class="container-title">
			<jdoc:include type="modules" name="title" />
		</div>
	</header>
<?php endif; ?>
<?php if ((!$statusFixed) && ($this->countModules('status'))) : ?>
	<!-- Begin Status Module -->
	<div id="status" class="navbar status-top hidden-phone">
		<div class="btn-toolbar">
			<jdoc:include type="modules" name="status" style="no" />
		</div>
		<div class="clearfix"></div>
	</div>
	<!-- End Status Module -->
<?php endif; ?>
<?php if (!$cpanel) : ?>
	<!-- Subheader -->
	<a class="btn btn-subhead" data-toggle="collapse" data-target=".subhead-collapse"><?php echo JText::_('TPL_ISIS_TOOLBAR'); ?>
		<span class="icon-wrench"></span></a>
	<div class="subhead-collapse collapse">
		<div class="subhead">
			<div class="container-fluid">
				<div id="container-collapse" class="container-collapse"></div>
				<div class="row-fluid">
					<div class="span12">
						<jdoc:include type="modules" name="toolbar" style="no" />
					</div>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>
	<div style="margin-bottom: 20px"></div>
<?php endif; ?>
<!-- container-fluid -->
<div class="container-fluid container-main">
	<section id="content">
		<!-- Begin Content -->
		<jdoc:include type="modules" name="top" style="xhtml" />
		<div class="row-fluid">
			<?php if ($showSubmenu) : ?>
			<div class="span2">
				<jdoc:include type="modules" name="submenu" style="none" />
			</div>
			<div class="span10">
				<?php else : ?>
				<div class="span12">
					<?php endif; ?>
					<jdoc:include type="message" />
					<?php
					// Show the page title here if the header is hidden
					if (!$displayHeader) : ?>
						<h1 class="content-title"><?php echo JHtml::_('string.truncate', $app->JComponentTitle, 0, false, false); ?></h1>
					<?php endif; ?>
					<jdoc:include type="component" />
				</div>
			</div>
			<?php if ($this->countModules('bottom')) : ?>
				<jdoc:include type="modules" name="bottom" style="xhtml" />
			<?php endif; ?>
			<!-- End Content -->
	</section>

	<?php if (!$this->countModules('status') || (!$statusFixed && $this->countModules('status'))) : ?>
		<footer class="footer">
			<p align="center">
				<jdoc:include type="modules" name="footer" style="no" />
				&copy; <?php echo $sitename; ?> <?php echo date('Y'); ?></p>
		</footer>
	<?php endif; ?>
</div>
<?php if (($statusFixed) && ($this->countModules('status'))) : ?>
	<!-- Begin Status Module -->
	<div id="status" class="navbar navbar-fixed-bottom hidden-phone">
		<div class="btn-toolbar">
			<div class="btn-group pull-right">
				<p>
					<jdoc:include type="modules" name="footer" style="no" />
					&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?>
				</p>

			</div>
			<jdoc:include type="modules" name="status" style="no" />
		</div>
	</div>
	<!-- End Status Module -->
<?php endif; ?>
<jdoc:include type="modules" name="debug" style="none" />
<?php if ($stickyToolbar) : ?>
	<script>
		jQuery(function($)
		{

			var navTop;
			var isFixed = false;

			processScrollInit();
			processScroll();

			$(window).on('resize', processScrollInit);
			$(window).on('scroll', processScroll);

			function processScrollInit()
			{
				if ($('.subhead').length) {
					navTop = $('.subhead').length && $('.subhead').offset().top - <?php echo ($displayHeader || !$statusFixed) ? 30 : 20;?>;

					// Fix the container top
					$(".container-main").css("top", $('.subhead').height() + $('nav.navbar').height());

					// Only apply the scrollspy when the toolbar is not collapsed
					if (document.body.clientWidth > 480)
					{
						$('.subhead-collapse').height($('.subhead').height());
						$('.subhead').scrollspy({offset: {top: $('.subhead').offset().top - $('nav.navbar').height()}});
					}
				}
			}

			function processScroll()
			{
				if ($('.subhead').length) {
					var scrollTop = $(window).scrollTop();
					if (scrollTop >= navTop && !isFixed) {
						isFixed = true;
						$('.subhead').addClass('subhead-fixed');

						// Fix the container top
						$(".container-main").css("top", $('.subhead').height() + $('nav.navbar').height());
					} else if (scrollTop <= navTop && isFixed) {
						isFixed = false;
						$('.subhead').removeClass('subhead-fixed');
					}
				}
			}
		});
	</script>
<?php endif; ?>
</body>
</html>
