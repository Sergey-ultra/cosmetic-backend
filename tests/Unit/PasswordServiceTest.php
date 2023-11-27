<?php

namespace Tests\Unit;

use App\Configuration;
use App\Services\PasswordService\PasswordService;
use PHPUnit\Framework\TestCase;

class PasswordServiceTest extends TestCase
{
    private PasswordService $passwordService;

    public function setUp(): void
    {
        parent::setUp();

        $configuration = $this->createMock(Configuration::class);
        $configuration->expects($this->any())
            ->method('getSaltMasterPassword')
            ->willReturn('c0210sovsecontej2022');

        $this->passwordService = new PasswordService($configuration);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example(): void
    {
        $masterPassword = $this->passwordService->generateGlobalMasterPassword();

        $this->assertTrue($this->passwordService->isMasterPassword($masterPassword));
    }
}
