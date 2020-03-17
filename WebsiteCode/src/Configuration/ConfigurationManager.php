<?php

namespace itechTest\Components\Configuration;


use itechTest\Components\Contracts\CanUseApplication;

/**
 * Class ConfigurationManager
 *
 * @package itechTest\Components\Configuration
 */
class ConfigurationManager extends CanUseApplication
{
    /**
     * @var array
     */
    private $configurations = [];

    /**
     * This method will load all the configurations from the available files
     */
    public function loadConfigurations(): void
    {
        $configurationPath = $this->getApplication()->getConfigFolderPath();
        $availableConfigurations = [];
        if (file_exists($configurationPath) && is_dir($configurationPath)) {
            foreach (new \DirectoryIterator($configurationPath) as $file) {
                if ($file->isFile()) {
                    $baseName = \strtolower($file->getBasename('.php'));
                    $realPath = $file->getRealPath();

                    // load values from the config file
                    $values = require $realPath;

                    if (\is_array($values)) {
                        $values = $this->addBaseNameToConfigValues($values, $baseName);

                        /*
                         * Programming Logic
                         * An inbuilt function called array_merge could have been used, however it is slow, especially
                         * when used in a loop
                         */
                        array_walk($values, function ($value, $key) use (&$availableConfigurations) {
                            $availableConfigurations[$key] = $value;
                        });
                    }
                }
            }
        }
        $this->configurations = $availableConfigurations;
    }

    /**
     * @return array
     */
    public function getRawConfiguration(): array
    {
        return $this->configurations;
    }

    /**
     * @param array  $values
     * @param string $baseName
     *
     * @return array
     */
    private function addBaseNameToConfigValues(array $values, string $baseName): array
    {
        $values = (array)\array_flip($values);

        $values = array_map(function (string $value) use ($baseName) {
            return "$baseName.$value";
        }, $values);

        // flip the keys
        $values = (array)\array_flip($values);

        return $values;

    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        $configurations = $this->getRawConfiguration();
        return $configurations[$key] ?? $default;
    }
}