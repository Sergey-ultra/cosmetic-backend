<?php

namespace App\Services\PasswordService;

use App\Configuration;

class PasswordService
{
    public function __construct(protected Configuration $configuration){}

    /**
     * @param string $password
     * @return bool
     */
    public function isMasterPassword(string $password): bool
    {
        return $password === $this->generateGlobalMasterPassword();
    }

    /**
     * @param \DateTimeInterface|null $date
     * @return string|false
     */
    public function generateGlobalMasterPassword(\DateTimeInterface $date = null): string|false
    {
        if (!$date) {
            $date = new \DateTime();
        }
        $str = $date->format('Ymd');
        return substr(md5($this->configuration->getSaltMasterPassword() . $str), -10);
    }
}
