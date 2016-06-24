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
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\AbstractType::buildForm()
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('searchTerm', SearchType::class, array(
			'label' => 'stinger_soft_entity_search.forms.query.term.label' 
		));
		
		$usedFacets = $options['used_facets'];
		$result = $options['result'];
		if($usedFacets && !$result) {
			foreach($usedFacets as $facetType) {
				$builder->add('facet_' . $facetType, FacetType::class, array(
					'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
					'multiple' => true,
					'expanded' => true 
				));
			}
		}
		if($result){
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
	 * {@inheritDoc}
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
	protected function createFacets(FormBuilderInterface $builder, FacetSet $facets) {
		foreach($facets->getFacets() as $facetType => $facetValues) {
			$builder->add('facet_' . $facetType, FacetType::class, array(
				'label' => 'stinger_soft_entity_search.forms.query.' . $facetType . '.label',
				'multiple' => true,
				'expanded' => true,
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
	 * @see \Symfony\Component\Form\AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefault('data_class', Query::class);
		$resolver->setDefault('translation_domain', 'StingerSoftEntitySearchBundle');
		$resolver->setDefault('used_facets', array());
		$resolver->setDefault('result', null);
	}
}
