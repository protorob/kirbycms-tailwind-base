<?php

use Kirby\Cms\App as Kirby;
use Kirby\Content\Field;

// $field->toBlocksHtml(): renders a blocks field like ->toBlocks()->toHtml(),
// then resolves the Panel's internal-link permalinks (/@/page/<uuid>,
// /@/file/<uuid>) in every href/src to real URLs.
//
// Kirby's core block snippets output writer HTML as-is, so those permalinks
// would otherwise reach the frontend and rely on Kirby's `@/page/...` route,
// which only resolves UUIDs already in the UUID cache — a page link 404s on
// a fresh cache (e.g. right after a deploy). permalinksToUrls() looks the
// UUID up properly (index fallback), and running it once over the whole
// output covers every core block (text, heading, list, quote, captions)
// without overriding each block snippet. See README's "Default page content".
Kirby::plugin('base/blocks-html', [
	'fieldMethods' => [
		'toBlocksHtml' => function (Field $field): string {
			$html = $field->toBlocks()->toHtml();

			if (str_contains($html, '/@/') === false) {
				return $html;
			}

			return (new Field($field->parent(), $field->key(), $html))
				->permalinksToUrls()
				->value();
		},
	],
]);
