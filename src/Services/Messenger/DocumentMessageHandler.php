<?php

namespace StingerSoft\EntitySearchBundle\Services\Messenger;

use Doctrine\ORM\EntityManagerInterface;
use StingerSoft\EntitySearchBundle\Model\Message\DocumentMessage;
use StingerSoft\EntitySearchBundle\Model\Message\RemoveDocumentMessage;
use StingerSoft\EntitySearchBundle\Model\Message\SaveDocumentMessage;
use StingerSoft\EntitySearchBundle\Services\SearchService;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DocumentMessageHandler {

	public function __construct(protected readonly SearchService $searchService, protected readonly EntityManagerInterface $em) {
	}

	public function __invoke(DocumentMessage $message): void {
		if($message instanceof SaveDocumentMessage) {
			$this->searchService->saveDocument($message->getDocument());
			$this->em->flush();
		}
		if($message instanceof RemoveDocumentMessage) {
			$this->searchService->removeDocument($message->getDocument());
			$this->em->flush();
		}
	}
}