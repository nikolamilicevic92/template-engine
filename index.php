<?php

require __DIR__ . '/template-engine/TemplateEngine.php';

TemplateEngine::$viewsSource = __DIR__ . '/views/src/';
TemplateEngine::$viewsOutput = __DIR__ . '/views/dist/';

TemplateEngine::render('demo');

