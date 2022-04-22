<?php

namespace Szczyglis\ChainParser\Contract;

use Szczyglis\ChainParser\Core\DataBag;

/**
 * Interface LoggerInterface
 * @package Szczyglis\ChainParser\Contract
 */
interface LoggerInterface
{
    /**
     * @param string $message
     * @param array $additionalData
     * @return mixed
     */
    public function addMessage(string $message, array $additionalData = []);

    /**
     * @return array
     */
    public function getMessages(): array;

    public function onBegin();

    public function onEnd();

    public function onIterationBegin();

    public function onIterationEnd();

    /**
     * @param DataBag $data
     * @return mixed
     */
    public function setData(DataBag $data);

    /**
     * @return string
     */
    public function getName(): string;
}