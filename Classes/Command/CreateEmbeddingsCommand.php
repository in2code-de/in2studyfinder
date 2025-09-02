<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Command;

use In2code\In2studyfinder\Ai\Service\Embedding\HandlerService;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateEmbeddingsCommand extends Command
{
    public const EMBEDDING_JSON_URL = '/fileadmin/in2studyfinder/chatbot/embeddings/studyCourses.json';
    public const EMBEDDING_TITLE_FIELDNAME = 'title_embedding';
    public const EMBEDDING_DESCRIPTION_FIELDNAME = 'teaser_embedding';

    protected HandlerService $handlerService;
    protected LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        HandlerService $handlerService,
        string $name = null
    ) {
        $this->logger = $logger;
        $this->handlerService = $handlerService;
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this->setDescription('Create embeddings for study course titles and teaser');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting to create embeddings for study courses...');

        // Fetch study courses from the database
        $studyCourses = $this->getStudyCourses();

        if (empty($studyCourses)) {
            $output->writeln('No study courses found.');
            return Command::FAILURE;
        }

        $output->writeln(sprintf('Found %d study courses.', count($studyCourses)));
        $this->handlerService->create($studyCourses, StudyCourse::TABLE, ['title', 'teaser']);
        $output->writeln('Embeddings saved');
        return Command::SUCCESS;
    }

    private function getStudyCourses(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_in2studyfinder_domain_model_studycourse');

        try {
            return $queryBuilder
                ->select('uid', 'title', 'teaser')
                ->from('tx_in2studyfinder_domain_model_studycourse')
                ->execute()
                ->fetchAllAssociative();
        } catch (Throwable $throwable) {
            $this->logger->error('Error fetching studycourses: ' . $throwable);
            return [];
        }
    }
}
