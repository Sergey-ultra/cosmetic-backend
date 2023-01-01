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
                    ConfigurationOption::get()->toArray(),
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

    public function set(string $key, $value)
    {
        ConfigurationOption::updateOrCreate(['key' => $key], ['value' => $value]);

        $this->parameters[$key] = $value;
    }

    public function getWeekStatus(): bool
    {
        return (bool)$this->get('week_status', false);
    }

    public function setWeekStatus(bool $value): void
    {
        $value = $value ? 'true' : 'false';
        $this->set('week_status', $value);
    }
}
