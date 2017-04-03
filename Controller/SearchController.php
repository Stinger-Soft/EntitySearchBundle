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
namespace StingerSoft\EntitySearchBundle\Controller;

use StingerSoft\EntitySearchBundle\Form\QueryType;
use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\PaginatableResultSet;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapperInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Basic implementation of a search controller to offer a frontend access to the search service
 */
class SearchController extends Controller {

	/**
	 * Maximum number of suggestions which should be provided by the autocomplete action
	 *
	 * @var integer
	 */
	const SUGGESTION_COUNT = 10;

	/**
	 * Prefix to store information in the session variable
	 *
	 * @var string
	 */
	const SESSION_PREFIX = 'stinger_soft_entity_search';

	/**
	 * Number of results which should be displayed per page
	 *
	 * @var integer
	 */
	const RESULTS_PER_PAGE = 10;

	/**
	 *
	 * @var SearchService
	 */
	private $searchService;

	public function searchAction(Request $request) {
		if($request->query->get('term', false) !== false) {
			$this->setSearchTerm($request->getSession(), $request->query->get('term'));
			return $this->redirectToRoute('stinger_soft_entity_search_search');
		}
		
		$term = $this->getSearchTerm($request->getSession());
		$facets = $this->getSearchFacets($request->getSession());
		
		$query = new Query($term, $facets, $this->getAvailableFacets());
		
		$facetForm = $this->createForm(QueryType::class, $query, array(
			'used_facets' => $query->getUsedFacets() 
		));
		
		$facetForm->handleRequest($request);
		if($facetForm->isSubmitted()) {
			if($facetForm->get('clear')->isClicked()) {
				$query->setFacets($this->getDefaultFacets());
			}
			$this->setSearchTerm($request->getSession(), $query->getSearchTerm());
			$this->setSearchFacets($request->getSession(), $query->getFacets());
		}
		$result = $this->getSearchService()->search($query);
		
		$facetForm = $this->createForm(QueryType::class, $query, array(
			'result' => $result,
			'used_facets' => $query->getUsedFacets(),
			'facet_formatter' => $this->getFacetFormatter() 
		));
		
		$page = $request->query->get('page', 1);
		$results = array();
		if($result instanceof PaginatableResultSet) {
			$results = $result->paginate($page, self::RESULTS_PER_PAGE);
		} else {
			$results = $result->getResults(($page - 1) * self::RESULTS_PER_PAGE, self::RESULTS_PER_PAGE);
		}
		
		return $this->render('StingerSoftEntitySearchBundle:Search:results.html.twig', array(
			'results' => $results,
			'resultSet' => $result,
			'term' => $query->getSearchTerm(),
			'mapper' => $this->get(DocumentToEntityMapperInterface::SERVICE_ID),
			'facetForm' => $facetForm->createView() 
		));
	}

	protected function getFacetFormatter() {
		return null;
	}

	/**
	 * Returns an array of preselected facets (no facet selected is the default)
	 *
	 * @return string[string][]
	 */
	protected function getDefaultFacets() {
		$availableFacets = $this->getAvailableFacets();
		return array_combine($availableFacets, array_fill(0, count($availableFacets), array()));
	}

	/**
	 * Returns an array of the available facets which should be offered the user as a filter
	 *
	 * @return string[]
	 */
	protected function getAvailableFacets() {
		return array(
			Document::FIELD_AUTHOR,
			Document::FIELD_EDITORS,
			Document::FIELD_TYPE 
		);
	}

	/**
	 * Fetches the searched facets from the session object
	 *
	 * @param SessionInterface $session        	
	 * @return string[string][]
	 */
	protected function getSearchFacets(SessionInterface $session) {
		$facets = $session()->get(self::SESSION_PREFIX . '_facets', false);
		return $facets ? json_decode($facets, true) : $this->getDefaultFacets();
	}

	/**
	 * Sets the given search facets in the user's session
	 * 
	 * @param SessionInterface $session
	 * @param string[string][] $facets
	 */
	protected function setSearchFacets(SessionInterface $session, $facets) {
		$session->set(self::SESSION_PREFIX . '_facets', json_encode($facets));
	}

	/**
	 * Fetches the search term from the session object
	 *
	 * @param SessionInterface $session        	
	 * @return mixed
	 */
	protected function getSearchTerm(SessionInterface $session) {
		return $session->get(self::SESSION_PREFIX . '_term', false);
	}

	/**
	 * Sets the given search term in the user's session
	 *
	 * @param SessionInterface $session        	
	 * @param string $term        	
	 */
	protected function setSearchTerm(SessionInterface $session, $term) {
		$session->set(self::SESSION_PREFIX . '_term', $term);
	}

	/**
	 * Returns a JSON array of autocompletions for the given term.
	 * The term can be provided as a GET or POST paramater with the name <em>term</em>
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function autocompleteAction(Request $request) {
		$term = $request->get('term');
		return new JsonResponse($this->getSearchService()->autocomplete($term, self::SUGGESTION_COUNT));
	}

	/**
	 * Provides an online help for the configured search service.
	 * If the search service has no online help defined a warning message will be displayed to the user.
	 *
	 * @param Request $request        	
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function onlineHelpAction(Request $request) {
		$template = $this->getSearchService()->getOnlineHelp($request->getLocale(), $this->getDefaultLocale());
		return $this->render($template ?: 'StingerSoftEntitySearchBundle:Help:no_help.html.twig');
	}

	/**
	 * Inits and returns the configured search service
	 *
	 * @return SearchService
	 */
	protected function getSearchService() {
		$this->service = $this->get(SearchService::SERVICE_ID);
		$this->service->setObjectManager($this->getObjectManager());
		return $this->service;
	}
}