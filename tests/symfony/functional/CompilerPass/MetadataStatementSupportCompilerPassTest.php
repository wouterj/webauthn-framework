<?php

declare(strict_types=1);

namespace Webauthn\Tests\Bundle\Functional\CompilerPass;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\Bundle\DependencyInjection\Compiler\MetadataStatementSupportCompilerPass;
use Webauthn\CertificateChainChecker\CertificateChainChecker;
use Webauthn\MetadataService\MetadataStatementRepository;
use Webauthn\MetadataService\StatusReportRepository;

/**
 * @internal
 */
final class MetadataStatementSupportCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function androidSafetyNetApiVerificationIsEnabledWhenAllServicesAndParametersAreSet(): void
    {
        //Given
        $this->setDefinition(AuthenticatorAttestationResponseValidator::class, new Definition());

        $this->setDefinition('metadata_statement_repository', new Definition());
        $this->container->setAlias(MetadataStatementRepository::class, 'metadata_statement_repository');

        $this->setDefinition('certificate_chain_checker', new Definition());
        $this->container->setAlias(CertificateChainChecker::class, 'certificate_chain_checker');

        $this->setDefinition('status_report_repository', new Definition());
        $this->container->setAlias(StatusReportRepository::class, 'status_report_repository');

        //When
        $this->compile();

        //Then
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            AuthenticatorAttestationResponseValidator::class,
            'enableMetadataStatementSupport',
            [
                new Reference(MetadataStatementRepository::class),
                new Reference(StatusReportRepository::class),
                new Reference(CertificateChainChecker::class),
            ]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MetadataStatementSupportCompilerPass());
    }
}
