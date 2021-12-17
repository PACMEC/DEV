<?php

namespace Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve;

class PresetCurve
{
    public $curve;
    public $g;
    public $n;
    public $hash;
    function __construct($options)
    {
        if ($options["type"] === "short") {
            $this->curve = new \Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\ShortCurve($options);
        } elseif ($options["type"] === "edwards") {
            $this->curve = new \Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\EdwardsCurve($options);
        } else {
            $this->curve = new \Ethereumico\EthereumWallet\Dependencies\Elliptic\Curve\MontCurve($options);
        }
        $this->g = $this->curve->g;
        $this->n = $this->curve->n;
        $this->hash = isset($options["hash"]) ? $options["hash"] : null;
    }
}
