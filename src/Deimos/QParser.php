<?php

/**
 * @author REZ1DENT3
 * https://github.com/REZ1DENT3/QParser
 *
 * @author tj
 * https://github.com/tj/php-selector
 */

namespace Deimos;

class QParser
{

    /**
     * @var \DOMDocument|null
     */
    protected $dom = null;

    /**
     * @var \DOMXpath|null
     */
    protected $xpath = null;

    /**
     * QParser constructor.
     * @param $html string|\DOMDocument|null
     */
    public function __construct($html = null)
    {
        if ($html) {
            $this->load($html);
        }
    }

    /**
     * @param $html string|\DOMDocument
     */
    public function load($html)
    {

        if (is_string($html)) {

            if (file_exists($html)) {
                $html = file_get_contents($html);
            }

            $this->dom = new \DOMDocument();
            ob_start();
            $this->dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            ob_get_clean();
        }
        else if ($html instanceof \DOMDocument) {
            $this->dom = $html;
        }
        else {
            throw new \InvalidArgumentException(__FUNCTION__);
        }

        $this->xpath = new \DOMXpath($this->dom);

    }

    /**
     * @param $selector
     * @param bool $asArray
     * @return array|mixed
     */
    public function find($selector, $asArray = true)
    {
        $elements = $this->xpath->evaluate($this->toXPath($selector));
        if ($asArray) {
            return $this->toArray($elements);
        }
        return $elements;
    }

    /**
     * @param $selector
     * @return mixed
     */
    protected function getSelectors($selector)
    {

        if (empty($selector)) {
            throw new \InvalidArgumentException(__FUNCTION__);
        }

        $removeSpacesAroundOperators = array(
            array('/\s*>\s*/', '>'),
            array('/\s*~\s*/', '~'),
            array('/\s*\+\s*/', '+'),
            array('/\s*,\s*/', ',')
        );

        foreach ($removeSpacesAroundOperators as $removeSpace) {
            $selector = preg_replace($removeSpace[0], $removeSpace[1], $selector);
        }

        return preg_split('/\s+(?![^\[]+\])/', $selector);

    }

    /**
     * @param $selector
     * @return mixed
     */
    protected function toXPath($selector)
    {

        $selectors = $this->getSelectors($selector);
        $rules = array(

            // ,
            array('/,/', '|descendant-or-self::'),

            // input:checked, :disabled, etc.
            array('/(.+)?:(checked|disabled|required|autofocus)/', '\1[@\2="\2"]'),

            // input:autocomplete, :autocomplete
            array('/(.+)?:(autocomplete)/', '\1[@\2="on"]'),

            // input:button, input:submit, etc.
            array('/:(text|password|checkbox|radio|button|submit|reset|file|hidden|image|datetime|datetime-local|date|month|time|week|number|range|email|url|search|tel|color)/', 'input[@type="\1"]'),

            // foo[id]
            array('/(\w+)\[([_\w-]+[_\w\d-]*)\]/', '\1[@\2]'),

            // [id]
            array('/\[([_\w-]+[_\w\d-]*)\]/', '*[@\1]'),

            // foo[id=foo]
            array('/\[([_\w-]+[_\w\d-]*)=[\'"]?(.*?)[\'"]?\]/', '[@\1="\2"]'),

            // [id=foo]
            array('/^\[/', '*['),

            // div#foo
            array('/([_\w-]+[_\w\d-]*)\#([_\w-]+[_\w\d-]*)/', '\1[@id="\2"]'),

            // #foo
            array('/\#([_\w-]+[_\w\d-]*)/', '*[@id="\1"]'),

            // div.foo
            array('/([_\w-]+[_\w\d-]*)\.([_\w-]+[_\w\d-]*)/', '\1[contains(concat(" ",@class," ")," \2 ")]'),

            // .foo
            array('/\.([_\w-]+[_\w\d-]*)/', '*[contains(concat(" ",@class," ")," \1 ")]'),

            // div:first-child
            array('/([_\w-]+[_\w\d-]*):first-child/', '*/\1[position()=1]'),

            // div:last-child
            array('/([_\w-]+[_\w\d-]*):last-child/', '*/\1[position()=last()]'),

            // :first-child
            array('/:first-child/', '*/*[position()=1]'),

            // :last-child
            array('/:last-child/', '*/*[position()=last()]'),

            // :nth-last-child
            array('/:nth-last-child\((\d+)\)/', '[position()=(last() - (\1 - 1))]'),

            // div:nth-child
            array('/([_\w-]+[_\w\d-]*):nth-child\((\d+)\)/', '*/*[position()=\2 and self::\1]'),

            // :nth-child
            array('/:nth-child\((\d+)\)/', '*/*[position()=\1]'),

            // :contains(Foo)
            array('/([_\w-]+[_\w\d-]*):contains\((.*?)\)/', '\1[contains(string(.),"\2")]'),

            // >
            array('/>/', '/'),

            // ~
            array('/~/', '/following-sibling::'),

            // +
            array('/\+([_\w-]+[_\w\d-]*)/', '/following-sibling::\1[position()=1]'),
            array('~\]\*~', ']'),
            array('~\]/\*~', ']'),

        );

        foreach ($selectors as &$selector) {
            foreach ($rules as $rule) {
                $selector = preg_replace($rule[0], $rule[1], $selector);
            }
        }

        // ' '
        $selector = implode('/descendant::', $selectors);
        $selector = 'descendant-or-self::' . $selector;

        // :scope
        $selector = preg_replace('/(((\|)?descendant-or-self::):scope)/', '.\3', $selector);

        // $element
        $sub_selectors = explode(',', $selector);

        foreach ($sub_selectors as $key => $sub_selector) {
            $parts = explode('$', $sub_selector);
            $sub_selector = array_shift($parts);

            if (count($parts) && preg_match_all('/((?:[^\/]*\/?\/?)|$)/', $parts[0], $matches)) {
                $results = $matches[0];
                $results[] = str_repeat('/..', count($results) - 2);
                $sub_selector .= implode('', $results);
            }

            $sub_selectors[$key] = $sub_selector;
        }

        return implode(',', $sub_selectors);

    }

    /**
     * @param $element \DOMNodeList|\DOMNode
     * @return array
     */
    protected function toArray($element)
    {

        if (isset($element->nodeName)) {

            $array = array(
                'name' => $element->nodeName,
                '@attributes' => array(),
                'text' => $element->textContent,
                'children' => $this->toArray($element->childNodes)
            );

            if ($element->attributes->length) {
                foreach ($element->attributes as $key => $attr) {
                    $array['@attributes'][$key] = $attr->value;
                }
            }

            return $array;

        }

        $array = array();

        for ($i = 0, $length = $element->length; $i < $length; ++$i) {
            if ($element->item($i)->nodeType == XML_ELEMENT_NODE) {
                $array[] = $this->toArray($element->item($i));
            }
        }

        return $array;

    }


}