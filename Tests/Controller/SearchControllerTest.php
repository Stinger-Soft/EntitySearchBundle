<?php
/**
 * Created by PhpStorm.
 * User: FlorianMeyer
 * Date: 06.03.2019
 * Time: 09:48
 */

namespace StingerSoft\EntitySearchBundle\Tests\Controller;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Psr\Container\ContainerInterface;
use StingerSoft\EntitySearchBundle\Controller\SearchController;
use StingerSoft\EntitySearchBundle\Form\FacetType;
use StingerSoft\EntitySearchBundle\Form\QueryType;
use StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapper;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use Symfony\Component\Form\Extension\HttpFoundation\Type\FormTypeHttpFoundationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\ResolvedFormTypeFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SearchControllerTest extends AbstractORMTestCase {

	/**
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function testAutocomplete(): void {
		$request = new Request(['term' => 'He']);
		$searchService = $this->getDummySearchService();

		$controller = $this->getSearchController($searchService);
		$response = $controller->autocompleteAction($request);
		$objResponse = \json_decode($response->getContent(), true);
		$this->assertEmpty($objResponse);

		$this->indexBeer($searchService);
		$response = $controller->autocompleteAction($request);
		$objResponse = \json_decode($response->getContent(), true);
		$this->assertCount(1, $objResponse);
	}

	public function testNewSearch(): void {
		$request = new Request(['term' => 'He']);
		$session = new Session(new MockArraySessionStorage());
		$request->setSession($session);

		$controller = $this->getSearchController();

		$response = $controller->searchAction($request, $this->getDocumentToEntityMapper());
		$this->assertInstanceOf(RedirectResponse::class, $response);
		$this->assertEquals('He', $session->get('stinger_soft_entity_search_term'));
	}

	public function render(): Response {
		return new Response();
	}

	public function testSearch(): void {
		$request = new Request();
		$session = new Session(new MockArraySessionStorage());
		$session->set('stinger_soft_entity_search_term', 'He');
		$request->setSession($session);

		$controller = $this->getSearchController();

		$response = $controller->searchAction($request, $this->getDocumentToEntityMapper());
		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals(200, $response->getStatusCode());

	}

	protected function getSearchController(?SearchService $searchService = null): SearchController {
		$searchService = $searchService ?? $this->getDummySearchService();

		$controller = $this->getMockBuilder(SearchController::class)->setMethods(array(
			'redirectToRoute',
			'getParameter'
		))->disableOriginalConstructor()->getMock();

		$controller->method('redirectToRoute')->willReturn(new RedirectResponse('https://www.stinger-soft.net'));
		$controller->method('getParameter')->willReturn([]);

		$that = $this;

		$container = $this->getMockBuilder(ContainerInterface::class)->setMethods([
			'get', 'has'
		])->disableOriginalConstructor()->getMockForAbstractClass();
		$container->method('has')->willReturnCallback(function($id) {
			return \in_array($id, ['twig', 'form.factory'], true);
		});
		$container->method('get')->willReturnCallback(function($id) use ($that) {
			if($id === 'form.factory') {
				$resolvedFormTypeFactory = new ResolvedFormTypeFactory();
				$formTypeFactory = Forms::createFormFactoryBuilder()
					->addType(new FacetType())
					->addType(new QueryType())
					->addTypeExtension(new FormTypeHttpFoundationExtension())
					->setResolvedTypeFactory($resolvedFormTypeFactory)
					->getFormFactory();
				return $formTypeFactory;
			}
			if($id === 'twig') {
				return $that;
			}
		});

		$controller->setSearchService($searchService);
		/** @noinspection PhpInternalEntityUsedInspection */
		$controller->setContainer($container);

		return $controller;
	}

	/**
	 * @param SearchService $service
	 * @param string $title
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	protected function indexBeer(SearchService $service, $title = 'Hemelinger'): void {
		$beer = new Beer();
		$beer->setTitle($title);
		$this->em->persist($beer);
		$this->em->flush();

		$document = $service->createEmptyDocumentFromEntity($beer);
		$beer->indexEntity($document);
		$service->saveDocument($document);
	}

	/**
	 *
	 * @return \StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapper
	 */
	protected function getDocumentToEntityMapper(): DocumentToEntityMapper {
		$managerMock = $this->getMockBuilder(AbstractManagerRegistry::class)->setMethods(array(
			'getManagerForClass'
		))->disableOriginalConstructor()->getMockForAbstractClass();
		$managerMock->method('getManagerForClass')->willReturn($this->em);
		return new DocumentToEntityMapper($managerMock);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Tests\AbstractTestCase::getUsedEntityFixtures()
	 */
	protected function getUsedEntityFixtures(): array {
		return array(
			Beer::class,
		);
	}

}