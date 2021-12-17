<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory;

use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo\CheckLocktimeVerify;
use Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo\CheckSequenceVerify;
class TimeLock
{
    /**
     * @var CheckLocktimeVerify|CheckSequenceVerify
     */
    private $info;
    /**
     * TimeLock constructor.
     * @param CheckLocktimeVerify|CheckSequenceVerify $info
     */
    public function __construct($info)
    {
        if (\is_object($info)) {
            $class = \get_class($info);
            if ($class !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo\CheckLocktimeVerify::class && $class !== \Ethereumico\EthereumWallet\Dependencies\BitWasp\Bitcoin\Transaction\Factory\ScriptInfo\CheckSequenceVerify::class) {
                throw new \RuntimeException("Invalid script info for TimeLock, must be CLTV/CSV");
            }
        } else {
            throw new \RuntimeException("Invalid script info for TimeLock, must be a script info object");
        }
        $this->info = $info;
    }
    /**
     * @return CheckLocktimeVerify|CheckSequenceVerify
     */
    public function getInfo()
    {
        return $this->info;
    }
}
