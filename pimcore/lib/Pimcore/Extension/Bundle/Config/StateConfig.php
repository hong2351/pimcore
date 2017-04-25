<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Extension\Bundle\Config;

use Pimcore\Config as PimcoreConfig;
use Pimcore\Extension\Config;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StateConfig
{
    /**
     * @var OptionsResolver
     */
    private static $optionsResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Sets the normalized bundle state on the extension manager config
     *
     * @param string $bundle
     * @param array $options
     */
    public function setState(string $bundle, array $options)
    {
        $config = $this->config->loadConfig();
        if (!isset($config->bundle)) {
            $config->bundle = new PimcoreConfig\Config([], true);
        }

        $entry = [];
        if (isset($config->bundle->$bundle)) {
            $entry = $config->bundle->$bundle;
        }

        $entry = array_merge($entry, $options);
        $entry = self::normalizeOptions($entry);

        $config->bundle->$bundle = $entry;

        $this->config->saveConfig($config);
    }

    /**
     * Lists enabled bundles from config
     *
     * @return array
     */
    public function getEnabledBundles(): array
    {
        $result  = [];
        $bundles = $this->getBundlesFromConfig();

        foreach ($bundles as $bundleName => $options) {
            if ($options['enabled']) {
                $result[$bundleName] = $options;
            }
        }

        return $result;
    }

    /**
     * Lists enabled bundle names from config
     *
     * @return array
     */
    public function getEnabledBundleNames(): array
    {
        return array_keys($this->getEnabledBundles());
    }

    /**
     * Loads bundles which are defined in configuration
     *
     * @return array
     */
    private function getBundlesFromConfig(): array
    {
        $config = $this->config->loadConfig();
        if (!isset($config->bundle)) {
            return [];
        }

        $bundles = $config->bundle->toArray();

        $result = [];
        foreach ($bundles as $bundleName => $options) {
            $result[$bundleName] = self::normalizeOptions($options);
        }

        return $result;
    }

    /**
     * Normalizes options array as expected in extension manager config
     *
     * @param array|bool $options
     *
     * @return array
     */
    final public static function normalizeOptions($options): array
    {
        if (is_bool($options)) {
            $options = ['enabled' => $options];
        } elseif (!is_array($options)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected options as bool or as array, but got %s',
                is_object($options) ? get_class($options) : gettype($options)
            ));
        }

        $resolver = self::getOptionsResolver();
        $options  = $resolver->resolve($options);

        return $options;
    }

    private static function getOptionsResolver(): OptionsResolver
    {
        if (null !== self::$optionsResolver) {
            return self::$optionsResolver;
        }

        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'enabled'      => false,
            'priority'     => 0,
            'environments' => []
        ]);

        $resolver->setRequired(['enabled', 'priority', 'environments']);

        $resolver->setAllowedTypes('enabled', 'bool');
        $resolver->setAllowedTypes('priority', 'int');
        $resolver->setAllowedTypes('environments', 'array');

        $resolver->setNormalizer('environments', function (Options $options, $value) {
            return array_map(function ($item) {
                return (string)$item;
            }, $value);
        });

        self::$optionsResolver = $resolver;

        return self::$optionsResolver;
    }
}
