<?php

namespace PHPSelector;

use PHPUnit\Framework\TestCase;

class OverloadSelector extends Dom
{
    public function testToXPath($selector)
    {
        return $this->toXPath($selector);
    }
}

class DomTest extends TestCase
{

    public function getHtml() : string
    {
        return '<div id="article" class="block large">
          <h2>Article Name</h2>
          <p>Contents of article</p>
          <ul>
            <li>One</li>
            <li>Two</li>
            <li>Three</li>
            <li>Four</li>
            <li><a href="#">Five</a></li>
          </ul>
        </div>';
    }

    public function testFindElements()
    {
        $tests = [
            ['*', 12],
            ['div', 1],
            ['div, p', 2],
            ['div , p', 2],
            ['div ,p', 2],
            ['div, p, ul li a', 3],
            ['div#article', 1],
            ['div#article.block', 1],
            ['div#article.large.block', 1],
            ['h2', 1],
            ['div h2', 1],
            ['div > h2', 1],
            ['ul li a', 1],
            ['ul > li > a', 1],
            ['a[href=#]', 1],
            ['a[href="#"]', 1],
            ['div[id="article"]', 1],
            ['h2:contains(Article)', 1],
            ['h2:contains(Article) + p', 1],
            ['h2:contains(Article) + p:contains(Contents)', 1],
            ['div p + ul', 1],
            ['ul li', 5],
            ['li ~ li', 4],
            ['li ~ li ~ li', 3],
            ['li + li', 4],
            ['li + li + li', 3],
            ['li:first-child', 1],
            ['li:last-child', 1],
            ['li:contains(One):first-child', 1],
            ['li:nth-child(2)', 1],
            ['li:nth-child(3)', 1],
            ['li:nth-child(4)', 1],
            ['li:nth-child(6)', 0],
            ['li:nth-last-child(2)', 1],
            ['li:nth-last-child(3)', 1],
            ['li:nth-last-child(4)', 1],
            ['li:nth-last-child(6)', 0],
            ['ul $li > a', 1],
            [':scope ul $li > a', 1]
        ];

        $dom = new OverloadSelector($this->getHtml());

        foreach ($tests as $test) {
            $c = count($dom->find($test[0]));
            $this->assertEquals($c, $test[1]);
        }

    }

    public function testToXPath()
    {
        $tests = [
            ['foo', 'descendant-or-self::foo'],
            ['foo, bar', 'descendant-or-self::foo|descendant-or-self::bar'],
            ['foo bar', 'descendant-or-self::foo/descendant::bar'],
            ['foo    bar', 'descendant-or-self::foo/descendant::bar'],
            ['foo > bar', 'descendant-or-self::foo/bar'],
            ['foo >bar', 'descendant-or-self::foo/bar'],
            ['foo>bar', 'descendant-or-self::foo/bar'],
            ['foo> bar', 'descendant-or-self::foo/bar'],
            ['div#foo', 'descendant-or-self::div[@id="foo"]'],
            ['#foo', 'descendant-or-self::*[@id="foo"]'],
            ['div.foo', 'descendant-or-self::div[contains(concat(" ",@class," "]," foo ")]'],
            ['.foo', 'descendant-or-self::*[contains(concat(" ",@class," "]," foo ")]'],
            ['[id]', 'descendant-or-self::*[@id]'],
            ['[id=bar]', 'descendant-or-self::*[@id="bar"]'],
            ['foo[id=bar]', 'descendant-or-self::foo[@id="bar"]'],
            ['[style=color: red; border: 1px solid black;]', 'descendant-or-self::*[@style="color: red; border: 1px solid black;"]'],
            ['foo[style=color: red; border: 1px solid black;]', 'descendant-or-self::foo[@style="color: red; border: 1px solid black;"]'],
            [':button', 'descendant-or-self::input[@type="button"]'],
            ['textarea', 'descendant-or-self::textarea'],
            [':submit', 'descendant-or-self::input[@type="submit"]'],
            [':first-child', 'descendant-or-self::*/*[position()=1]'],
            ['div:first-child', 'descendant-or-self::*/div[position()=1]'],
            [':last-child', 'descendant-or-self::*/*[position()=last()]'],
            [':nth-last-child(2)', 'descendant-or-self::[position()=(last() - (2 - 1))]'],
            ['div:last-child', 'descendant-or-self::*/div[position()=last()]'],
            [':nth-child(2)', 'descendant-or-self::*/*[position()=2]'],
            ['div:nth-child(2)', 'descendant-or-self::*/*[position()=2 and self::div]'],
            ['foo + bar', 'descendant-or-self::foo/following-sibling::bar[position()=1]'],
            ['li:contains(Foo)', 'descendant-or-self::li[contains(string(.],"Foo")]'],

            ['foo bar baz', 'descendant-or-self::foo/descendant::bar/descendant::baz'],
            ['foo + bar + baz', 'descendant-or-self::foo/following-sibling::bar[position()=1]/following-sibling::baz[position()=1]'],
            ['foo > bar > baz', 'descendant-or-self::foo/bar/baz'],
            ['p ~ p ~ p', 'descendant-or-self::p/following-sibling::p/following-sibling::p'],
            ['div#article p em', 'descendant-or-self::div[@id="article"]/descendant::p/descendant::em'],
            ['div.foo:first-child', 'descendant-or-self::div[contains(concat(" ",@class," "]," foo ")][position()=1]'],
            ['form#login > input[type=hidden]._method', 'descendant-or-self::form[@id="login"]/input[@type="hidden"][contains(concat(" ",@class," "]," _method ")]']
        ];

        $dom = new OverloadSelector();
        foreach ($tests as $test) {
            $this->assertEquals($test[1], $dom->testToXPath($test[0]), 'Selector '.$test[0].' is not selecting the right element');
        }
    }

}