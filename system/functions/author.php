<?php


function get_author_information( $raw = false ) {
	global $core;

	$author = $core->config->get('author');

	// allowed fields; see https://microformats.org/wiki/h-card
	// TODO: we need to test those fields
	$additional_hcard_properties = array(
		'p-name',
		'p-honorific-prefix',
		'p-given-name',
		'p-additional-name',
		'p-family-name',
		'p-sort-string',
		'p-honorific-suffix',
		'p-nickname',
		'u-email',
		'u-logo',
		'u-photo',
		'u-url',
		'u-uid',
		'p-category',
		'p-adr',
		//'h-adr',
		'p-post-office-box',
		'p-extended-address',
		'p-street-address',
		'p-locality',
		'p-region',
		'p-postal-code',
		'p-country-name',
		'p-label',
		'p-geo',
		'u-geo',
		//'h-geo',
		'p-latitude',
		'p-longitude',
		'p-altitude',
		'p-tel',
		'p-note',
		'dt-bday',
		'u-key',
		'p-org',
		'p-job-title',
		'p-role',
		'u-impp',
		'p-sex',
		'p-gender-identity',
		'dt-anniversary',
		//'u-sound'
	);

	// make sure to only include allowed fields:
	foreach( $author as $field_name => $field_content ) {
		if( ! in_array($field_name, $additional_hcard_properties) ) {
			continue;
		}
	}


	if( ! $raw ) {

		// "special" named fields:
		if( ! empty($author['p-name']) ) $author['display_name'] = $author['p-name'];
		if( ! empty($author['p-given-name']) ) $author['given_name'] = $author['p-given-name'];
		if( ! empty($author['p-last-name']) ) $author['family_name'] = $author['p-last-name'];
		if( ! empty($author['p-note']) ) $author['description'] = $author['p-note'];
		if( ! empty($author['u-email']) ) $author['email'] = $author['u-email'];
		if( ! empty($author['u-url']) ) $author['url'] = $author['u-url'];
		if( ! empty($author['u-photo']) ) $author['avatar'] = $author['u-photo'];

		if( ! empty($author['given_name']) && ! empty($author['family_name']) && empty($author['display_name']) ) $author['display_name'] = $author['given_name'].' '.$author['family_name'];

	}

	return $author;
}