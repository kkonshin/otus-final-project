<?php

declare(strict_types=1);

namespace App\Containers\Common\ContainerTree\UI\CLI\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command as CMD;

// TODO Добавить генерацию и регистрацию
//  файлов провайдеров в зависимости от названия контейнера.
final class MakeContainerTree extends Command
{
    private const DIRECTORIES = [
        'Actions',
        'Contracts',
        'Data',
        'Data/Migrations',
        'Data/Rules',
        'Data/Structures',
        'Data/Transporters',
        'Exceptions',
        'Jobs',
        'Models',
        'Providers',
        'Tasks',
        'UI',
        'UI/API',
        'UI/API/Controllers',
        'UI/API/Routes',
        'UI/API/Transformers',
    ];

    private const ADDITIONAL_DIRECTORIES = [
        'Data/Repositories',
        'UI/CLI',
        'UI/CLI/Commands',
        'UI/WEB',
        'UI/WEB/Controllers',
        'UI/WEB/Routes',
        'UI/WEB/Views',
    ];

    /** @var string */
    protected $signature = 'make:container-tree
                            {container : Имя контейнера}
                            {--full : Создать полную структуру директорий}';

    /** @var string */
    protected $description = 'Создает структуру директорий контейнера';

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(private Filesystem $filesystem)
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        try {
            $containerName = $this->argument('container');

            $isFull = $this->option('full');

            $this->info(
                ' ----------------------------------------------'
                . PHP_EOL
                . "  Создаем структуру контейнера для: $containerName"
                . PHP_EOL
                . ' ----------------------------------------------'
            );

            // Create the base container directory
            $containerPath = app_path("Containers/$containerName");

            if ($this->filesystem->exists($containerPath)) {
                $this->error("Контейнер $containerName уже существует!");
                return CMD::FAILURE;
            }

            $this->filesystem->makeDirectory($containerPath, 0755, true);

            $this->createStructure($containerPath, $isFull);

            $this->info("Контейнер $containerName успешно создан!");

            return CMD::SUCCESS;
        } catch (Exception $e) {
            $this->error("Ошибка создания контейнера: {$e->getMessage()}");

            report($e);

            return CMD::FAILURE;
        }
    }

    /**
     * @param string $containerPath
     * @param bool $isFull
     * @return void
     */
    private function createStructure(string $containerPath, bool $isFull = false): void
    {
        $directories = $isFull
            ? array_merge(self::DIRECTORIES, self::ADDITIONAL_DIRECTORIES)
            : self::DIRECTORIES;

        foreach ($directories as $directory) {
            $path = "$containerPath/$directory";
            $this->filesystem->makeDirectory($path, 0755, true);
            $this->line("Создана: $directory");
        }
    }
}
