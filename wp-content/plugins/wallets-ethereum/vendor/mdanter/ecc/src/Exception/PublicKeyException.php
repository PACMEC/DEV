<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Exception;

use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint;
use Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface;
use Throwable;
class PublicKeyException extends \RuntimeException
{
    /**
     * @var GeneratorPoint
     */
    private $G;
    /**
     * @var PointInterface
     */
    private $point;
    public function __construct(\Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint $G, \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface $point, string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $this->G = $G;
        $this->point = $point;
        parent::__construct($message, $code, $previous);
    }
    public function getGenerator() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\GeneratorPoint
    {
        return $this->G;
    }
    public function getPoint() : \Ethereumico\EthereumWallet\Dependencies\Mdanter\Ecc\Primitives\PointInterface
    {
        return $this->point;
    }
}
