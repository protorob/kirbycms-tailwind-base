<?php

use JohannSchopplich\Helpers\Env;

// Load secrets (API keys etc.) from the gitignored .env in the project root —
// config files are read before plugins boot, so env() needs this explicit
// load here. Skipped when no .env exists (Dotenv would throw otherwise).
if (is_file(dirname(__DIR__, 2) . '/.env')) {
	Env::load(dirname(__DIR__, 2));
}

return [
	// Disabled while actively adding/removing icons in assets/icons/ during
	// development: the plugin caches its folder scan keyed by field config
	// (not by folder contents), so a new SVG never invalidates the cache on
	// its own — see README's "Site panel defaults".
	'tobimori.icon-field' => [
		'cache' => false
	],

	// Global block list for every `type: blocks` field. Kirby's default is
	// just the core blocks below; custom blocks (site/blueprints/blocks/,
	// site/snippets/blocks/) only show up in the Panel once listed here.
	// Setting this replaces the default list, so the core blocks are repeated.
	'blocks.fieldsets' => [
		'code'     => 'blocks/code',
		'gallery'  => 'blocks/gallery',
		'heading'  => 'blocks/heading',
		'image'    => 'blocks/image',
		'line'     => 'blocks/line',
		'list'     => 'blocks/list',
		'markdown' => 'blocks/markdown',
		'quote'    => 'blocks/quote',
		'table'    => 'blocks/table',
		'text'     => 'blocks/text',
		'video'    => 'blocks/video',

		'child-pages' => 'blocks/child-pages',
	],

	// SEO via kirby-helpers: meta tags come from each page's SEO tab (falling
	// back to Site → SEO), rendered in header.php via $page->meta(). See
	// README's "SEO: meta tags, sitemap and robots.txt".
	'johannschopplich.helpers' => [
		'meta' => [
			// Keep the error page out of search results.
			'defaults' => fn ($kirby, $site, $page) => $page->isErrorPage()
				? ['robots' => 'noindex']
				: [],
		],
		'robots' => [
			'enabled' => true,
		],
		'sitemap' => [
			'enabled' => true,
			'exclude' => [
				'templates' => ['error'],
				// Pages set to noindex in their SEO tab shouldn't be advertised
				// in sitemap.xml either (the plugin matches these as regexes).
				'pages' => fn () => site()->index()
					->filter(fn ($page) => str_contains($page->robots()->value() ?? '', 'noindex'))
					->map(fn ($page) => preg_quote($page->id(), '!'))
					->values(),
			],
		],
	],
];
