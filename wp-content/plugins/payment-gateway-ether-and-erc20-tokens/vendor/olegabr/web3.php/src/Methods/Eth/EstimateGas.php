<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Methods\Eth;

use InvalidArgumentException;
use Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod;
use Ethereumico\Epg\Dependencies\Web3\Validators\TransactionValidator;
use Ethereumico\Epg\Dependencies\Web3\Formatters\TransactionFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\BigNumberFormatter;
class EstimateGas extends \Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [\Ethereumico\Epg\Dependencies\Web3\Validators\TransactionValidator::class];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\Epg\Dependencies\Web3\Formatters\TransactionFormatter::class];
    /**
     * outputFormatters
     * 
     * @var array
     */
    protected $outputFormatters = [\Ethereumico\Epg\Dependencies\Web3\Formatters\BigNumberFormatter::class];
    /**
     * defaultValues
     * 
     * @var array
     */
    protected $defaultValues = [];
    /**
     * construct
     * 
     * @param string $method
     * @param array $arguments
     * @return void
     */
    // public function __construct($method='', $arguments=[])
    // {
    //     parent::__construct($method, $arguments);
    // }
}
