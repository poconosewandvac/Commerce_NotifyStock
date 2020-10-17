<?php

$snips = [
    'commerce_notifystock.add_request_hook',
];

$snippets = [];
$idx = 0;

foreach ($snips as $name => $description) {
    $idx++;
    $snippets[$idx] = $modx->newObject('modSnippet');
    $snippets[$idx]->fromArray(array(
        'name' => $name,
        'description' => $description . ' (Part of Commerce)',
        'snippet' => getSnippetContent($sources['snippets'] . strtolower($name) . '.snippet.php')
    ));
}

return $snippets;
