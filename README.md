# PHP Selector

[![Latest Stable Version](https://poser.pugx.org/bkuhl/php-selector/v/stable.png)](https://packagist.org/packages/bkuhl/php-selector) [![Total Downloads](https://poser.pugx.org/bkuhl/php-selector/downloads.png)](https://packagist.org/packages/bkuhl/php-selector) [![Build Status](https://travis-ci.org/bkuhl/php-selector.svg?branch=master)](https://travis-ci.org/bkuhl/php-selector) 

A simple DOM query library originally written by [tj](https://github.com/tj/php-selector).

## Usage

Given the sample html:

```PHP
$html = <<<HTML
<div id="article" class="block large">
  <h2>Article Name</h2>
  <p>Contents of article</p>
  <ul>
    <li>One</li>
    <li>Two</li>
    <li>Three</li>
    <li>Four</li>
    <li><a href="#">Five</a></li>
  </ul>
</div>
HTML;
```
  
The following will return an array of elements:

```PHP
$dom = new \PHPSelector\Dom($html);

var_dump($dom->find('div#article.large'));
var_dump($dom->find('div > h2:contains(Article)'));
var_dump($dom->find('div p + ul'));
var_dump($dom->find('ul > li:first-child'));
var_dump($dom->find('ul > li ~ li'));
var_dump($dom->find('ul > li:last-child'));
var_dump($dom->find('li a[href=#]'));
```