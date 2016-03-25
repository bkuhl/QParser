=
PHP Selector

Quick DOM query library I whipped up for an old
PHP data miner I had which needed more flexibility.

Current supports most CSS3 selectors.

Tested with php 5.4

== Examples

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
    $qParser = new \Deimos\QParser($html);
    
    var_dump($qParser->find('div#article.large'));
    var_dump($qParser->find('div > h2:contains(Article)'));
    var_dump($qParser->find('div p + ul'));
    var_dump($qParser->find('ul > li:first-child'));
    var_dump($qParser->find('ul > li ~ li'));
    var_dump($qParser->find('ul > li:last-child'));
    var_dump($qParser->find('li a[href=#]'));
```