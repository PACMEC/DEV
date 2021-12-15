<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Web3\Methods\Eth;

use InvalidArgumentException;
use Web3\Methods\EthMethod;
use Web3\Formatters\FeeHistoryFormatter;

use Web3\Formatters\QuantityFormatter;
use Web3\Formatters\OptionalQuantityFormatter;
use Web3\Formatters\FloatArrayFormatter;

use Web3\Validators\BlockHashOrTagValidator;
use Web3\Validators\QuantityValidator;
use Web3\Validators\FloatArrayValidator;

class FeeHistory extends EthMethod
{
  /**
   * validators
   *
   * @var array
   */
  protected $validators = [
      QuantityValidator::class, BlockHashOrTagValidator::class, FloatArrayValidator::class
  ];

  /**
   * inputFormatters
   *
   * @var array
   */
  protected $inputFormatters = [
      QuantityFormatter::class, OptionalQuantityFormatter::class, FloatArrayFormatter::class
  ];

  /**
   * outputFormatters
   *
   * @var array
   */
  protected $outputFormatters = [
      FeeHistoryFormatter::class
  ];

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
