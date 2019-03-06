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

namespace StingerSoft\EntitySearchBundle\Tests\Form;

use StingerSoft\EntitySearchBundle\Form\FacetType;
use Symfony\Component\Form\Test\TypeTestCase;

class FacetTypeTest extends TypeTestCase {

	public function testForm(): void {

		$formData = [];
		$form = $this->factory->create(FacetType::class, $formData, array(
		));

		// submit the data to the form directly
		$form->submit($formData);

		$this->assertTrue($form->isSynchronized());
		$this->assertTrue($form->isValid());

	}

	public function testMultipleForm(): void {

		$formData = [];
		$form = $this->factory->create(FacetType::class, $formData, array(
			'multiple' => true,
		));

		// submit the data to the form directly
		$form->submit($formData);

		$this->assertTrue($form->isSynchronized());
		$this->assertTrue($form->isValid());

	}

}