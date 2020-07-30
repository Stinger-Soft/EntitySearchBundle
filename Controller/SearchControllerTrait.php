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

namespace StingerSoft\EntitySearchBundle\Controller;

use StingerSoft\EntitySearchBundle\Form\QueryType;
use StingerSoft\EntitySearchBundle\Model\PaginatableResultSet;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapperInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait SearchControllerTrait {

	/**
	 * @var SearchService
	 */
	protected $searchService;
	private $availableFacets;
	private $facetFormatter;

	public function searchAction(Request $request, DocumentToEntityMapperInterface $mapper) {
		if($request->query->get('term', false) !== false) {
			$this->setSearchTerm($request->getSession(), $request->query->get('term'));
			$this->removeFilterOptions($request->getSession());
			return $this->redirectToRoute('stinger_soft_entity_search_search');
		}

		$term = $this->getSearchTerm($request->getSession());
		$facets = $this->getSearchFacets($request->getSession());
		$availableFacets = $this->getAvailableFacets();

		$query = new Query($term, $facets, array_keys($availableFacets));

		$facetForm = $this->createForm(QueryType::class, $query, array(
			'used_facets' => $this->getConfiguredUsedFacets($query->getUsedFacets())
		));

		$facetForm->handleRequest($request);
		if($facetForm->isSubmitted()) {
			if($facetForm->get('clear')->isClicked()) {
				$query->setFacets($this->getDefaultFacets());
			}
			$this->setSearchTerm($request->getSession(), $query->getSearchTerm());
			$this->setSearchFacets($request->getSession(), $query->getFacets());
		} elseif($this->getUnsetFilterOptions($request->getSession())) {
			$query->setFacets($this->getDefaultFacets());
		}

		try {
			$result = $this->searchService->search($query);

			$facetForm = $this->createForm(QueryType::class, $query, array(
				'result'          => $result,
				'used_facets'     => $this->getConfiguredUsedFacets($query->getUsedFacets()),
				'facet_formatter' => $this->getFacetFormatter()
			));

			$page = (int)$request->query->get('page', 1);
			$results = null;
			if($result instanceof PaginatableResultSet) {
				$results = $result->paginate($page, $this->getResultsPerPage());
			} elseif($result !== null) {
				$results = $result->getResults(($page - 1) * $this->getResultsPerPage(), $this->getResultsPerPage());
			}

			return $this->render($this->getTemplate(), array(
				'results'   => $results,
				'resultSet' => $result,
				'term'      => $query->getSearchTerm(),
				'mapper'    => $mapper,
				'facetForm' => $facetForm->createView()
			));
		} catch(\Exception $exception) {
			$response = $this->render($this->getErrorTemplate(), array(
				'error' => $exception->getMessage(),
				'term'  => $query->getSearchTerm()
			));
			return $response->setStatusCode(500);
		}
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
		return new JsonResponse($this->searchService->autocomplete($term, $this->getSuggestionCount()));
	}

	/**
	 * Provides an online help for the configured search service.
	 * If the search service has no online help defined a warning message will be displayed to the user.
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function onlineHelpAction(Request $request) {
		$template = $this->searchService->getOnlineHelp($request->getLocale(), $this->getDefaultLocale());
		return $this->render($template ?: 'StingerSoftEntitySearchBundle:Help:no_help.html.twig');
	}

	/**
	 * @param SearchService $searchService
	 * @required
	 */
	public function setSearchService(SearchService $searchService): void {
		$this->searchService = $searchService;
	}

	protected function getConfiguredUsedFacets(array $queryUsedFacets) {
		$availableFacets = $this->getAvailableFacets();
		$usedFacets = array();
		foreach($queryUsedFacets as $queryUsedFacet) {
			$usedFacets[$queryUsedFacet] = $availableFacets[$queryUsedFacet];
		}
		return $usedFacets;
	}

	/**
	 * Maximum number of suggestions which should be provided by the autocomplete action
	 *
	 * @return integer
	 */
	protected function getSuggestionCount() {
		return 10;
	}

	/**
	 * Prefix to store information in the session variable
	 *
	 * @return string
	 */
	protected function getSessionPrefix() {
		return 'stinger_soft_entity_search';
	}

	/**
	 * Number of results which should be displayed per page
	 *
	 * @return integer
	 */
	protected function getResultsPerPage() {
		return 10;
	}

	protected function getTemplate() {
		return 'StingerSoftEntitySearchBundle:Search:results.html.twig';
	}

	protected function getErrorTemplate() {
		return 'StingerSoftEntitySearchBundle:Search:error.html.twig';
	}

	protected function getFacetFormatter() {
		$this->initFacets();
		return $this->facetFormatter;
	}

	/**
	 * Returns an array of preselected facets (no facet selected is the default)
	 *
	 * @return string[string][]
	 */
	protected function getDefaultFacets() {
		$availableFacets = $this->getAvailableFacets();
		return array_combine(array_keys($availableFacets), array_fill(0, count($availableFacets), array()));
	}

	/**
	 * Returns an array of the available facets which should be offered the user as a filter
	 *
	 * @return string[]
	 */
	protected function getAvailableFacets() {
		$this->initFacets();
		return $this->availableFacets;
	}

	protected function initFacets() {
		if(!$this->availableFacets) {
			$this->availableFacets = array();
			$this->facetFormatter = array();

			$facetServices = $this->getParameter('stinger_soft.entity_search.available_facets');
			foreach($facetServices as $facetServiceId) {
				$facetService = $this->searchService->getFacet($facetServiceId);
				$this->availableFacets[$facetService->getField()] = $facetService->getFormOptions();
				if($facetService->getFacetFormatter()) {
					$this->facetFormatter[$facetService->getField()] = $facetService->getFacetFormatter();
				}
			}
		}
	}

	/**
	 * Fetches the searched facets from the session object
	 *
	 * @param SessionInterface $session
	 * @return string[string][]
	 */
	protected function getSearchFacets(SessionInterface $session) {
		$facets = $session->get($this->getSessionPrefix() . '_facets', false);
		return $facets ? \json_decode($facets, true) : $this->getDefaultFacets();
	}

	protected function removeFilterOptions(SessionInterface $session) {
		$session->remove($this->getSessionPrefix() . '_filter_options');
	}

	protected function getUnsetFilterOptions(SessionInterface $session) {
		return $session->get($this->getSessionPrefix() . '_filter_options', true);
	}

	/**
	 * Sets the given search facets in the user's session
	 *
	 * @param SessionInterface $session
	 * @param string[string][] $facets
	 */
	protected function setSearchFacets(SessionInterface $session, $facets) {
		$session->set($this->getSessionPrefix() . '_facets', \json_encode($facets));
	}

	/**
	 * Fetches the search term from the session object
	 *
	 * @param SessionInterface $session
	 * @return mixed
	 */
	protected function getSearchTerm(SessionInterface $session) {
		return $session->get($this->getSessionPrefix() . '_term', false);
		$session->set($this->getSessionPrefix() . '_filter_options', false);
	}

	/**
	 * Sets the given search term in the user's session
	 *
	 * @param SessionInterface $session
	 * @param string $term
	 */
	protected function setSearchTerm(SessionInterface $session, $term) {
		$session->set($this->getSessionPrefix() . '_term', $term);
	}

}