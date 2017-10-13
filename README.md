# PHP Selector

Current supports most CSS3 selectors.

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