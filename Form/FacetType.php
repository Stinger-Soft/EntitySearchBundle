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
namespace StingerSoft\EntitySearchBundle\Form;

use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;
use StingerSoft\EntitySearchBundle\Model\ResultSet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
		$builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
			$data = $event->getForm()->getExtraData();
			$event->setData(array_unique(array_merge($data, $event->getData())));
		});
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::buildView()
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
	
	/**
	 *
	 * @var ResultSet $result
	 */
		// $result = $options['result'];
		// $view->vars['facetTypes'] = array_keys($result->getFacets()->getFacets());
	}

	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param FacetSet $facets        	
	 */
	protected function createFacets(FormBuilderInterface $builder, FacetSet $facets) {
		foreach($facets->getFacets() as $facetType => $facetValues) {
			$builder->add('facet_' . $facetType, ChoiceType::class, array(
				'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
				'multiple' => true,
				'expanded' => true,
				'choices_as_values' => true,
				'property_path' => 'facets[' . $facetType . ']',
				'choices' => $this->generateFacetChoices($facetType, $facetValues) 
			));
		}
	}

	/**
	 *
	 * @param string $facetType        	
	 * @param array $facets        	
	 */
	protected function generateFacetChoices($facetType, array $facets) {
		$choices = array();
		
		foreach($facets as $facet => $count) {
			if($count == 0)
				break;
			$choices[$facet . ' (' . $count . ')'] = $facet;
		}
		return $choices;
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
// 		$resolver->setDefault('data_class', 'array');
		$resolver->setDefault('translation_domain', 'StingerSoftEntitySearchBundle');
		$resolver->setDefault('by_reference', true);
	}
}
