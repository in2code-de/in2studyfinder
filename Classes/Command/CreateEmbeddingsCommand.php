<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Command;

use In2code\In2studyfinder\Ai\Adapter\MistralAdapter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateEmbeddingsCommand extends Command
{
    public const EMBEDDING_JSON_URL = '/fileadmin/in2studyfinder/chatbot/embeddings/studyCourses.json';
    public const EMBEDDING_TITLE_FIELDNAME = 'title_embedding';
    public const EMBEDDING_DESCRIPTION_FIELDNAME = 'teaser_embedding';

    protected MistralAdapter $mistralAdapter;
    protected LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        MistralAdapter $mistralAdapter,
        string $name = null
    ) {
        $this->logger = $logger;
        $this->mistralAdapter = $mistralAdapter;
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

        // Create embeddings
        $embeddings = [];

        foreach ($studyCourses as $studyCourse) {
            $output->writeln(sprintf('Processing study course: %s (ID: %d)', $studyCourse['title'], $studyCourse['uid']));

            // Create embedding for title
            $embeddingsResponse = $this->createEmbedding([$studyCourse['title'], $studyCourse['teaser']]);

            // Store embeddings
            $embeddings[$studyCourse['uid']] = [
                'uid' => $studyCourse['uid'],
                self::EMBEDDING_TITLE_FIELDNAME => $embeddingsResponse[0]['embedding'],
                self::EMBEDDING_DESCRIPTION_FIELDNAME => $embeddingsResponse[1]['embedding'],
            ];
        }

        // Save embeddings to JSON file in fileadmin
        $jsonFilePath = Environment::getPublicPath() . self::EMBEDDING_JSON_URL;
        $this->saveEmbeddingsToFile($embeddings, $jsonFilePath);

        $output->writeln(sprintf('Embeddings saved to %s', $jsonFilePath));

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

    private function createEmbedding(array $texts): array
    {
        try {
            return $this->mistralAdapter->createEmbedding($texts);
        } catch (Throwable $throwable) {
            $this->logger->error('Error creating embedding: ' . $throwable->getMessage());
            return [];
        }
    }

    private function saveEmbeddingsToFile(array $embeddings, string $filePath): void
    {
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            GeneralUtility::mkdir_deep($directory);
        }

        file_put_contents($filePath, json_encode($embeddings, JSON_PRETTY_PRINT));
    }
}
