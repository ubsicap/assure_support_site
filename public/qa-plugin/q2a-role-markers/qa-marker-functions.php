<?php

/**
 * Return the svg data of the badge icon, used for dynamic coloring
 *
 * @return string
 */
function qa_get_badge_svg($baseDir)
{
    $filePath = QA_HTML_THEME_LAYER_URLTOROOT . 'qa-shield-gen.svg';
    $svgFile = fopen($filePath, "r");
    $text = fread($svgFile,filesize($filePath));
    fclose($svgFile);
    return $text;
}