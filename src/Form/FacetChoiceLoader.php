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

namespace StingerSoft\EntitySearchBundle\Form;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class FacetChoiceLoader implements ChoiceLoaderInterface {

	/** @var ChoiceListInterface|null */
	private ?ChoiceListInterface $choiceList = null;

	private array $facets;

	public function __construct(array $facets) {
		$this->facets = $facets;
	}

	public function addFacets(array $facets): void {
		$this->facets = array_merge($this->facets, $facets);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface::loadValuesForChoices()
	 */
	public function loadValuesForChoices(array $choices, $value = null): array {
		// is called on form creat with $choices containing the preset of the bound entity
		$values = array();
		foreach($choices as $key => $choice) {
			// we use a DataTransformer, thus only plain values arrive as choices which can be used directly as value
			if(is_callable($value)) {
				$values[$key] = (string)call_user_func($value, $choice, $key);
			} else {
				$values[$key] = $choice;
			}
		}

		// this has to be done by yourself: array( label => value )
		// $labeledValues = MyLabelService::getLabels($values);

		// // create internal choice list from loaded values
		// $this->choiceList = new ArrayChoiceList($labeledValues, $value);

		return $values;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface::loadChoiceList()
	 */
	public function loadChoiceList($value = null): ChoiceListInterface {
		// is called on form view create after loadValuesForChoices of form create
		// if($this->choiceList instanceof ChoiceListInterface) {
		// return $this->choiceList;
		// }

		// if no values preset yet return empty list
		$this->choiceList = new ArrayChoiceList($this->facets, $value);
		return $this->choiceList;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface::loadChoicesForValues()
	 *
	 */
	public function loadChoicesForValues(array $values, $value = null): array {
		// is called on form submit after loadValuesForChoices of form create and loadChoiceList of form view create
		$choices = array();
		foreach($values as $key => $val) {
			// we use a DataTransformer, thus only plain values arrive as choices which can be used directly as value
			if(is_callable($value)) {
				$choices[$key] = (string)call_user_func($value, $val, $key);
			} else {
				$choices[$key] = $val;
			}
		}

		// this has to be done by yourself: array( label => value )
		// $labeledValues = MyLabelService::getLabels($values);

		// // reset internal choice list
		// $this->choiceList = new ArrayChoiceList($labeledValues, $value);

		return $choices;
	}
}
