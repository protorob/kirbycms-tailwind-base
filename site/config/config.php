<?php

return [
	// Disabled while actively adding/removing icons in assets/icons/ during
	// development: the plugin caches its folder scan keyed by field config
	// (not by folder contents), so a new SVG never invalidates the cache on
	// its own — see README's "Site panel defaults".
	'tobimori.icon-field' => [
		'cache' => false
	]
];
