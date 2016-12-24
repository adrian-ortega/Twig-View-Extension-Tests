<?php

namespace App\Twig\Extensions;

class FormHelpers
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('selected', [$this, 'selected']),
            new \Twig_SimpleFunction('checked', [$this, 'checked']),
        ];
    }

    public function _checked_selected($value, $current, $type)
    {
        if ((string)$value === (string)$current) {
            $result = " $type=\"$type\"";
        } else {
            $result = '';
        }

        return $result;
    }

    public function selected($value = '', $current = 0)
    {
        return $this->_checked_selected($value, $current, 'selected');
    }

    public function checked($value = '', $current = 0)
    {
        return $this->_checked_selected($value, $current, 'checked');
    }
}
