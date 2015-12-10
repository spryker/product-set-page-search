<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerEngine\Zed\Kernel\ClassResolver\DependencyContainer;

use SprykerEngine\Shared\Config;
use SprykerEngine\Zed\Kernel\ClassResolver\ClassInfo;
use SprykerFeature\Shared\System\SystemConfig;

class DependencyContainerNotFoundException extends \Exception
{

    /**
     * @param ClassInfo $callerClassInfo
     */
    public function __construct(ClassInfo $callerClassInfo)
    {
        parent::__construct($this->buildMessage($callerClassInfo));
    }

    /**
     * @param ClassInfo $callerClassInfo
     *
     * @return string
     */
    private function buildMessage(ClassInfo $callerClassInfo)
    {
        $message = 'Spryker Kernel Exception' . PHP_EOL;
        $message .= sprintf(
            'Can not resolve %2$sDependencyContainer in %s layer for your bundle "%s"',
            $callerClassInfo->getLayer(),
            $callerClassInfo->getBundle()
        ) . PHP_EOL;

        $message .= 'You can fix this by adding the missing DependencyContainer to your bundle.' . PHP_EOL;

        $message .= sprintf(
            'E.g. %s\\Zed\\%2$s\\%s\\%2$sDependencyContainer',
            Config::getInstance()->get(SystemConfig::PROJECT_NAMESPACE),
            $callerClassInfo->getBundle(),
            $callerClassInfo->getLayer()
        );

        return $message;
    }

}