<?php

namespace Oro\Bundle\EntityExtendBundle\Command;

use Oro\Bundle\EntityConfigBundle\Config\ConfigInterface;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Extend\EntityExtendUpdateProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The CLI command to update the database schema and all related caches to reflect changes made in extended entities.
 */
class UpdateCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'oro:entity-extend:update';

    /** @var EntityExtendUpdateProcessor */
    private $entityExtendUpdateProcessor;

    /** @var ConfigManager */
    private $configManager;

    /**
     * @param EntityExtendUpdateProcessor $entityExtendUpdateProcessor
     * @param ConfigManager               $configManager
     */
    public function __construct(
        EntityExtendUpdateProcessor $entityExtendUpdateProcessor,
        ConfigManager $configManager
    ) {
        parent::__construct();
        $this->entityExtendUpdateProcessor = $entityExtendUpdateProcessor;
        $this->configManager = $configManager;
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this
            ->setDescription(
                'Updates the database schema and all related caches to reflect changes made in extended entities.'
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Shows changes without applying them.');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dry-run')) {
            $this->showChanges($output);

            return 0;
        }

        return $this->applyChanges($output);
    }

    /**
     * @param OutputInterface $output
     */
    private function showChanges(OutputInterface $output): void
    {
        $changes = $this->getChanges();
        if (!$changes) {
            $output->writeln('<info>There are no any changes.</info>');

            return;
        }

        $output->writeln('The following entities have changes:');
        foreach ($changes as $entityClass => [$entityState, $fields]) {
            $output->writeln('');
            $output->writeln(sprintf('%s    <comment>%s</comment>', $entityClass, $entityState));
            $output->writeln(str_repeat('-', strlen($entityClass) + strlen($entityState) + 4));
            if ($fields) {
                $output->writeln('Fields:');
                $fieldTable = new Table($output);
                $fieldTable->setStyle(
                    (new TableStyle())
                        ->setHorizontalBorderChars('')
                        ->setVerticalBorderChars('')
                        ->setDefaultCrossingChar('')
                );
                foreach ($fields as $fieldName => $fieldState) {
                    $fieldTable->addRow([$fieldName, sprintf('<comment>%s</comment>', $fieldState)]);
                }
                $fieldTable->render();
            }
        }

        $output->writeln('');
        $output->writeln('To apply the changes run this command without <comment>--dry-run</comment> option.');
    }

    /**
     * @return array [entity class => [entity state, [field name => field stata, ...]], ...]
     */
    private function getChanges(): array
    {
        $changes = [];
        $configs = $this->configManager->getConfigs('extend');
        foreach ($configs as $config) {
            if ($this->isSchemaUpdateRequired($config)) {
                $entityClass = $config->getId()->getClassName();
                $fields = [];
                $fieldConfigs = $this->configManager->getConfigs('extend', $entityClass);
                foreach ($fieldConfigs as $fieldConfig) {
                    if (!$fieldConfig->is('state', ExtendScope::STATE_ACTIVE)) {
                        $fields[$fieldConfig->getId()->getFieldName()] = $fieldConfig->get('state');
                    }
                }
                ksort($fields);
                $changes[$entityClass] = [$config->get('state'), $fields];
            }
        }
        ksort($changes);

        return $changes;
    }

    /**
     * @param ConfigInterface $config
     *
     * @return bool
     */
    private function isSchemaUpdateRequired(ConfigInterface $config): bool
    {
        return
            $config->is('is_extend')
            && !$config->is('state', ExtendScope::STATE_ACTIVE)
            && !$config->is('is_deleted');
    }

    /**
     * @param OutputInterface $output
     *
     * @return int
     */
    private function applyChanges(OutputInterface $output): int
    {
        $output->writeln('<comment>Updating the database schema and all entity extend related caches ...</comment>');

        if (!$this->entityExtendUpdateProcessor->processUpdate()) {
            $output->writeln('<error>The update failed.</error>');

            return 1;
        }

        $output->writeln('<info>The update complete.</info>');

        return 0;
    }
}
