<?php

namespace App\Service;

class HalsteadMetrics
{
    /**
     * @var float
     */
    protected float $vocabulary;

    /**
     * @var float
     */
    protected float $size;

    /**
     * @param $numOperators
     * @param $numOperands
     * @param $numOperatorInstances
     * @param $numOperandInstances
     * @return float|null
     */
    public function estimateVolume($numOperators, $numOperands, $numOperatorInstances, $numOperandInstances): ?float
    {
        $this->vocabulary = $numOperators + $numOperands;
        $this->size = $numOperatorInstances + $numOperandInstances;

        return (float)$this->size * log((float)$this->vocabulary, 2);
    }
}