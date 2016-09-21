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
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QueryType extends AbstractType {

	/**
	 *
	 * @var array
	 */
	protected $defaultOptions = array();

	public function __construct(array $defaultOptions = array()) {
		$this->defaultOptions = $defaultOptions;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::buildForm()
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('searchTerm', SearchType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.term.label' 
		));
		
		$usedFacets = $options['used_facets'];
		$result = $options['result'];
		$preferredFilterChoices = $options['preferred_filter_choices'];
		$maxChoiceGroupCount = $options['max_choice_group_count'];
		$data = array();
		
		if($usedFacets && !$result) {
			foreach($usedFacets as $facetType) {
				$preferredChoices = isset($preferredFilterChoices[$facetType]) ? $preferredFilterChoices[$facetType] : array(); 
				
				$builder->add('facet_' . $facetType, FacetType::class, array(
					'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
					'multiple' => true,
					'expanded' => true,
					'preferred_choices' => function ($val) use ($preferredChoices, $data) {
						return count($preferredChoices) == 0 || in_array($val, $preferredChoices) || in_array($val, $data['facet_' . $facetType]);
					},
				));
			}
		}
		if($result) {
			$this->createFacets($builder, $result->getFacets());
		}
		
		$builder->add('filter', SubmitType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.filter.label' 
		));
		$builder->add('clear', SubmitType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.clear.label' 
		));
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::buildView()
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
		/**
		 *
		 * @var ResultSet $result
		 */
		$result = $options['result'];
		$view->vars['facetTypes'] = array_keys($result->getFacets()->getFacets());
	}

	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param FacetSet $facets        	
	 */
	protected function createFacets(FormBuilderInterface $builder, FacetSet $facets, array $options) {
		$preferredFilterChoices = $options['preferred_filter_choices'];
		$maxChoiceGroupCount = $options['max_choice_group_count'];
		$data = array();
		foreach($facets->getFacets() as $facetType => $facetValues) {
			$preferredChoices = isset($preferredFilterChoices[$facetType]) ? $preferredFilterChoices[$facetType] : array();
			
			$builder->add('facet_' . $facetType, FacetType::class, array(
				'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
				'multiple' => true,
				'expanded' => true,
				'choices' => $this->generateFacetChoices($facetType, $facetValues) ,
				'preferred_choices' => function ($val) use ($preferredChoices, $data) {
					return count($preferredChoices) == 0 || in_array($val, $preferredChoices) || in_array($val, $data['facet_' . $facetType]);
				},
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
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefault('data_class', Query::class);
		$resolver->setDefault('translation_domain', 'StingerSoftEntitySearchBundle');
		$resolver->setDefault('used_facets', array());
		$resolver->setDefault('result', null);
		
		$resolver->setRequired('preferred_filter_choices');
		$resolver->setDefault('preferred_filter_choices', isset($this->defaultOptions['preferred_filter_choices']) ? $this->defaultOptions['preferred_filter_choices'] : array());
		
		$resolver->setRequired('max_choice_group_count');
		$resolver->setDefault('max_choice_group_count', isset($this->defaultOptions['max_choice_group_count']) ? $this->defaultOptions['max_choice_group_count'] : 5);
	}
}
