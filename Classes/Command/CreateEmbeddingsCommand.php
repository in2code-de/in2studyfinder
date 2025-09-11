<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Command;

use In2code\In2studyfinder\Ai\Embedding\EmbeddingsHandler;
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
    protected EmbeddingsHandler $handlerService;
    protected LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        EmbeddingsHandler $handlerService,
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
        $this->handlerService->create($studyCourses, StudyCourse::TABLE);
        $output->writeln('Embeddings saved');
        return Command::SUCCESS;
    }

    private function getStudyCourses(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(StudyCourse::TABLE);

        try {
            return $queryBuilder
                ->select('uid', 'title', 'teaser')
                ->from(StudyCourse::TABLE)
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (Throwable $throwable) {
            $this->logger->error('Error fetching studycourses: ' . $throwable);
            return [];
        }
    }
}
