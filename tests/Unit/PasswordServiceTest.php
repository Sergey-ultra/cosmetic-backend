<?php

namespace Tests\Unit;

use App\Configuration;
use App\Services\PasswordService\PasswordService;
use PHPUnit\Framework\TestCase;

class PasswordServiceTest extends TestCase
{
    protected PasswordService $passwordService;
    public function setUp(): void
    {
        parent::setUp();
        $this->passwordService = new PasswordService(new Configuration());
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $masterPassword = $this->passwordService->generateGlobalMasterPassword();

        $this->assertTrue($this->passwordService->isMasterPassword($masterPassword));
    }
}
