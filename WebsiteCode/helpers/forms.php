<?php

if (!function_exists('build_select_html')) {

    /**
     * @param string $name
     * @param array  $items
     *
     * @param null   $currentValue
     *
     * @return string
     */
    function build_select_html(string $name, array $items, $currentValue = null)
    {

        $output = "<select name=\"$name\" class=\"form-control\">";
        foreach ($items as $item) {
            $selected = null !== $currentValue && $item === $currentValue;
            $selected = $selected ? 'selected' : '';
            $output .= "<option value=\"$item\" $selected>$item</option>";
        }
        $output .= '</select>';

        return $output;

    }
}