<?php
declare(strict_types=1);

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

interface FacetServiceInterface {

	public const TAG_NAME = 'stinger_soft_entity_search.facets';

	public function getField(): string;

	public function getFormOptions(): array;

	public function getFacetFormatter(): ?callable;
}