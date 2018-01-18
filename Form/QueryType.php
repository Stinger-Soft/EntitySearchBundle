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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

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
			$data = array();
			foreach($usedFacets as $facetType => $facetTypeOptions) {
				$preferredChoices = isset($preferredFilterChoices[$facetType]) ? $preferredFilterChoices[$facetType] : array();
				$i = 0;
				$builder->add('facet_' . $facetType, FacetType::class, array_merge(array(
					'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
					'multiple' => true,
					'expanded' => true,
					'allow_extra_fields' => true,
					'preferred_choices' => function ($val) use ($preferredChoices, $data, $facetType, $maxChoiceGroupCount, &$i) {
						return $i++ < $maxChoiceGroupCount || $maxChoiceGroupCount == 0 || in_array($val, $preferredChoices) || (isset($data['facet_' . $facetType]) && in_array($val, $data['facet_' . $facetType]));
					} 
				), $facetTypeOptions));
				unset($i);
			}
		}
		if($result) {
			$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options, $result) {
				$this->createFacets($event->getForm(), $result->getFacets(), $options, $event->getData());
			});
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
	 * @param FormBuilderInterface|Form $builder        	
	 * @param FacetSet $facets        	
	 */
	protected function createFacets($builder, FacetSet $facets, array $options, $data) {
		$preferredFilterChoices = $options['preferred_filter_choices'];
		$maxChoiceGroupCount = $options['max_choice_group_count'];
		$selectedFacets = $data->getFacets();
		$usedFacets = $options['used_facets'];
		
		foreach($facets->getFacets() as $facetType => $facetValues) {
			$preferredChoices = isset($preferredFilterChoices[$facetType]) ? $preferredFilterChoices[$facetType] : array();
			
			$i = 0;
			$facetTypeOptions = $usedFacets[$facetType];
			$builder->add('facet_' . $facetType, FacetType::class, array_merge(array(
				'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
				'multiple' => true,
				'expanded' => true,
				'allow_extra_fields' => true,
				'choices' => $this->generateFacetChoices($facetType, $facetValues, isset($selectedFacets[$facetType]) ? $selectedFacets[$facetType] : array(), $options['facet_formatter']),
				'preferred_choices' => function ($val) use ($preferredChoices, $selectedFacets, $facetType, $maxChoiceGroupCount, &$i) {
					return $i++ < $maxChoiceGroupCount || $maxChoiceGroupCount == 0 || in_array($val, $preferredChoices) || (isset($selectedFacets[$facetType]) && in_array($val, $selectedFacets[$facetType]));
				} 
			), $facetTypeOptions));
			unset($i);
		}
	}

	/**
	 *
	 * @param string $facetType        	
	 * @param array $facets        	
	 */
	protected function generateFacetChoices($facetType, array $facets, array $selectedFacets = array(), $formatter) {
		$choices = array();
		foreach($facets as $facet => $count) {
			if($count == 0 && !in_array($facet, $selectedFacets))
				continue;
			$choices[$this->formatFacet($formatter, $facetType, $facet, $count)] = $facet;
		}
		foreach($selectedFacets as $facet) {
			if(isset($facets[$facet])) continue;
			$count = 0;
			$choices[$this->formatFacet($formatter, $facetType, $facet, $count)] = $facet;
		}
		return $choices;
	}
	
	protected function formatFacet($formatter, $facetType, $facet, $count) {
		$default = $facet . ' (' . $count . ')';
		if(!$formatter) {
			return $default;
		}
		return call_user_func($formatter, $facetType, $facet, $count, $default);
		
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
		$resolver->setRequired('used_facets');
		$resolver->setDefault('result', null);
		
		$resolver->setRequired('preferred_filter_choices');
		$resolver->setDefault('preferred_filter_choices', isset($this->defaultOptions['preferred_filter_choices']) ? $this->defaultOptions['preferred_filter_choices'] : array());
		
		$resolver->setRequired('max_choice_group_count');
		$resolver->setDefault('max_choice_group_count', isset($this->defaultOptions['max_choice_group_count']) ? $this->defaultOptions['max_choice_group_count'] : 10);
		
		$resolver->setDefault('facet_formatter', null);
	}
}
