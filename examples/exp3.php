<?php

include_once '../vendor/autoload.php';

$html = <<<HTML
<main>
  <h1>Привет, мир!</h1>
  <ul>
    <li>Первый</li>
    <li>Второй</li>
    <li>Третий</li>
    <li>Четвертый</li>
    <li>Пятый</li>
  </ul>
</main>
HTML;

$qParser = new \Deimos\QParser($html);
var_dump($qParser->find('li'));