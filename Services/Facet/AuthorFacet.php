<?php

/*
 * This file is part of the Stinger Entity Search package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StingerSoft\EntitySearchBundle\Services\Facet;

use StingerSoft\EntitySearchBundle\Model\Document;

class AuthorFacet implements FacetServiceInterface {

	const SERVICE_ID = 'stinger_soft_entity_search.facets.author';

	public function getField() {
		return Document::FIELD_AUTHOR;
	}

	public function getFormOptions() {
		return array(
			'label'              => 'stinger_soft_entity_search.forms.query.author.label',
			'translation_domain' => 'StingerSoftEntitySearchBundle'
		);
	}

	public function getFacetFormatter() {
		return null;
	}

}