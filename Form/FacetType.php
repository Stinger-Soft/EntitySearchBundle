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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpKernel\Kernel;

class FacetType extends AbstractType {

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::buildForm()
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->resetModelTransformers();
		$builder->resetViewTransformers();
		
		if($options['multiple']) {
			$builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
				$event->stopPropagation();
			}, 1);
		}
		$builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
			$data = $event->getForm()->getExtraData();
			//$event->setData(array_unique(array_merge($data, $event->getData())));
			$event->setData($data);
		});
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::getParent()
	 */
	public function getParent() {
		return ChoiceType::class;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefault('translation_domain', 'StingerSoftEntitySearchBundle');
		$resolver->setDefault('by_reference', true);
	}
}
