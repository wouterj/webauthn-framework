<?php

declare(strict_types=1);

namespace Webauthn\Tests\Bundle\Functional;

use function array_key_exists;
use Assert\Assertion;
use Symfony\Component\Finder\Finder;
use Webauthn\MetadataService\Service\MetadataService;
use Webauthn\MetadataService\Statement\MetadataStatement;

final class SingleFileService implements MetadataService
{
    /**
     * @var array<string, MetadataStatement>
     */
    private array $statements;

    public function __construct(private string $rootPath)
    {
    }

    public function list(): iterable
    {
        $this->loadMDS();

        yield from array_keys($this->statements);
    }

    public function has(string $aaguid): bool
    {
        $this->loadMDS();

        return array_key_exists($aaguid, $this->statements);
    }

    public function get(string $aaguid): MetadataStatement
    {
        $this->loadMDS();

        Assertion::keyExists(
            $this->statements,
            $aaguid,
            sprintf('The MDS with the AAGUID "%s" does not exist.', $aaguid)
        );

        return $this->statements[$aaguid];
    }

    private function loadMDS(): void
    {
        foreach ($this->getFilenames() as $filename) {
            $data = file_get_contents($filename);
            $mds = MetadataStatement::createFromString($data);
            if ($mds->getAaguid() === null) {
                continue;
            }
            $this->statements[$mds->getAaguid()] = $mds;
        }
    }

    /**
     * @return string[]
     */
    private function getFilenames(): iterable
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->rootPath)
        ;

        foreach ($finder->files() as $file) {
            yield $file->getRealPath();
        }
    }
}
