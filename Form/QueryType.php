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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\ResultSet;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class QueryType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('searchTerm', SearchType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.term.label' 
		));
		/**
		 *
		 * @var ResultSet $result
		 */
		$result = $options['result'];
		$this->createFacets($builder, $result->getFacets());
		$builder->add('filter', SubmitType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.filter.label' 
		));
		$builder->add('clear', SubmitType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.clear.label' 
		));
	}

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
	 * {@inheritdoc}
	 *
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefault('data_class', Query::class);
		$resolver->setDefault('translation_domain', 'StingerSoftEntitySearchBundle');
		
		$resolver->setRequired('result', null);
		$resolver->setAllowedTypes('result', ResultSet::class);
		
		$resolver->setRequired('translator');
		$resolver->setAllowedTypes('translator', TranslatorInterface::class);
	}
}