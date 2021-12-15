<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\Epg\Dependencies\Web3\Methods\Personal;

use InvalidArgumentException;
use Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod;
use Ethereumico\Epg\Dependencies\Web3\Validators\TransactionValidator;
use Ethereumico\Epg\Dependencies\Web3\Validators\StringValidator;
use Ethereumico\Epg\Dependencies\Web3\Formatters\TransactionFormatter;
use Ethereumico\Epg\Dependencies\Web3\Formatters\StringFormatter;
class SendTransaction extends \Ethereumico\Epg\Dependencies\Web3\Methods\EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [\Ethereumico\Epg\Dependencies\Web3\Validators\TransactionValidator::class, \Ethereumico\Epg\Dependencies\Web3\Validators\StringValidator::class];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [\Ethereumico\Epg\Dependencies\Web3\Formatters\TransactionFormatter::class, \Ethereumico\Epg\Dependencies\Web3\Formatters\StringFormatter::class];
    /**
     * outputFormatters
     * 
     * @var array
     */
    protected $outputFormatters = [];
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
