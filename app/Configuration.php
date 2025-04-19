<?php


namespace App;


use App\Models\ConfigurationOption;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\ParameterBag;

class Configuration extends ParameterBag
{
    public function __construct()
    {
        $options = Cache::remember(
            'options',
            now()->addDay(),
            function () {
                return array_reduce(
                    ConfigurationOption::query()->get()->toArray(),
                    function ($carry, $item) {
                        $carry[$item['key']] = $item['value'];
                        return $carry;
                    },
                    []
                );
            }
        );

        parent::__construct($options);
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        ConfigurationOption::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        $this->parameters[$key] = $value;
    }


    /**
     * @param string $key
     * @param bool $value
     * @return void
     */
    public function setBoolean(string $key, bool $value): void
    {
        $value = $value ? 'true' : 'false';
        $this->set($key, $value);
    }

    /**
     * @return string
     * Получить соль для мастер-пароля
     */
    public function getSaltMasterPassword(): string
    {
        return (string)env('SALT_MASTER_PASSWORD', 'c0210sovsecontej2022');
    }
}
