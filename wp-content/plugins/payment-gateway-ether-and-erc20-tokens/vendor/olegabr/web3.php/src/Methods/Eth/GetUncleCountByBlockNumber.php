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
use Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator;
use Ethereumico\Epg\Dependencies\Web3\Formatters\OptionalQuantityFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\BigNumberFormatter;
class GetUncleCountByBlockNumber extends \Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [[\Ethereumico\Epg\Dependencies\Web3\Validators\TagValidator::class, \Ethereumico\Epg\Dependencies\Web3\Validators\QuantityValidator::class]];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\Epg\Dependencies\Web3\Formatters\OptionalQuantityFormatter::class];
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
    protected $defaultValues = ['latest'];
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
