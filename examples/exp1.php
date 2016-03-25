<?php

include_once '../vendor/autoload.php';

$html = <<<HTML
  <div>
    <label>
      <input type="radio" name="newsletter" value="Hot Fuzz">
      <span>name?</span>
    </label>
  </div>
  <div>
    <label>
      <input type="radio" name="newsletter" value="Cold Fusion">
      <span>value?</span>
    </label>
  </div>
  <div>
    <label>
      <input type="radio" name="newsletter" value="Evil Plans">
      <span>value?</span>
    </label>
  </div>
HTML;

$qParser = new \Deimos\QParser($html);
var_dump($qParser->find('div label [name][value="Evil Plans"]'));