<?php
namespace WbsVendors\Dgm\ClassNameAware;


class ClassNameAware
{
    public static function className()
    {
        return get_called_class();
    }
}