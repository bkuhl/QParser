<?php

namespace Deimos;

class OverloadQParser extends QParser
{
    public function testToXPath($selector)
    {
        return $this->toXPath($selector);
    }
}

class QParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return string
     */
    public function getHtml()
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
        $tests = array(
            array('*', 12),
            array('div', 1),
            array('div, p', 2),
            array('div , p', 2),
            array('div ,p', 2),
            array('div, p, ul li a', 3),
            array('div#article', 1),
            array('div#article.block', 1),
            array('div#article.large.block', 1),
            array('h2', 1),
            array('div h2', 1),
            array('div > h2', 1),
            array('ul li a', 1),
            array('ul > li > a', 1),
            array('a[href=#]', 1),
            array('a[href="#"]', 1),
            array('div[id="article"]', 1),
            array('h2:contains(Article)', 1),
            array('h2:contains(Article) + p', 1),
            array('h2:contains(Article) + p:contains(Contents)', 1),
            array('div p + ul', 1),
            array('ul li', 5),
            array('li ~ li', 4),
            array('li ~ li ~ li', 3),
            array('li + li', 4),
            array('li + li + li', 3),
            array('li:first-child', 1),
            array('li:last-child', 1),
            array('li:contains(One):first-child', 1),
            array('li:nth-child(2)', 1),
            array('li:nth-child(3)', 1),
            array('li:nth-child(4)', 1),
            array('li:nth-child(6)', 0),
            array('li:nth-last-child(2)', 1),
            array('li:nth-last-child(3)', 1),
            array('li:nth-last-child(4)', 1),
            array('li:nth-last-child(6)', 0),
            array('ul $li > a', 1),
            array(':scope ul $li > a', 1)
        );

        $qp = new OverloadQParser($this->getHtml());

        foreach ($tests as $test) {
            $c = count($qp->find($test[0]));
            $this->assertEquals($c, $test[1]);
        }

    }

    public function testToXPath()
    {
        $tests = array(
            array('foo', 'descendant-or-self::foo'),
            array('foo, bar', 'descendant-or-self::foo|descendant-or-self::bar'),
            array('foo bar', 'descendant-or-self::foo/descendant::bar'),
            array('foo    bar', 'descendant-or-self::foo/descendant::bar'),
            array('foo > bar', 'descendant-or-self::foo/bar'),
            array('foo >bar', 'descendant-or-self::foo/bar'),
            array('foo>bar', 'descendant-or-self::foo/bar'),
            array('foo> bar', 'descendant-or-self::foo/bar'),
            array('div#foo', 'descendant-or-self::div[@id="foo"]'),
            array('#foo', 'descendant-or-self::*[@id="foo"]'),
            array('div.foo', 'descendant-or-self::div[contains(concat(" ",@class," ")," foo ")]'),
            array('.foo', 'descendant-or-self::*[contains(concat(" ",@class," ")," foo ")]'),
            array('[id]', 'descendant-or-self::*[@id]'),
            array('[id=bar]', 'descendant-or-self::*[@id="bar"]'),
            array('foo[id=bar]', 'descendant-or-self::foo[@id="bar"]'),
            array('[style=color: red; border: 1px solid black;]', 'descendant-or-self::*[@style="color: red; border: 1px solid black;"]'),
            array('foo[style=color: red; border: 1px solid black;]', 'descendant-or-self::foo[@style="color: red; border: 1px solid black;"]'),
            array(':button', 'descendant-or-self::input[@type="button"]'),
            array('textarea', 'descendant-or-self::textarea'),
            array(':submit', 'descendant-or-self::input[@type="submit"]'),
            array(':first-child', 'descendant-or-self::*/*[position()=1]'),
            array('div:first-child', 'descendant-or-self::*/div[position()=1]'),
            array(':last-child', 'descendant-or-self::*/*[position()=last()]'),
            array(':nth-last-child(2)', 'descendant-or-self::[position()=(last() - (2 - 1))]'),
            array('div:last-child', 'descendant-or-self::*/div[position()=last()]'),
            array(':nth-child(2)', 'descendant-or-self::*/*[position()=2]'),
            array('div:nth-child(2)', 'descendant-or-self::*/*[position()=2 and self::div]'),
            array('foo + bar', 'descendant-or-self::foo/following-sibling::bar[position()=1]'),
            array('li:contains(Foo)', 'descendant-or-self::li[contains(string(.),"Foo")]'),

            array('foo bar baz', 'descendant-or-self::foo/descendant::bar/descendant::baz'),
            array('foo + bar + baz', 'descendant-or-self::foo/following-sibling::bar[position()=1]/following-sibling::baz[position()=1]'),
            array('foo > bar > baz', 'descendant-or-self::foo/bar/baz'),
            array('p ~ p ~ p', 'descendant-or-self::p/following-sibling::p/following-sibling::p'),
            array('div#article p em', 'descendant-or-self::div[@id="article"]/descendant::p/descendant::em'),
            array('div.foo:first-child', 'descendant-or-self::div[contains(concat(" ",@class," ")," foo ")][position()=1]'),
            array('form#login > input[type=hidden]._method', 'descendant-or-self::form[@id="login"]/input[@type="hidden"][contains(concat(" ",@class," ")," _method ")]')
        );

        $qp = new OverloadQParser($this->getHtml());
        foreach ($tests as $test) {
            $this->assertEquals($test[1], $qp->testToXPath($test[0]));
        }
    }

}