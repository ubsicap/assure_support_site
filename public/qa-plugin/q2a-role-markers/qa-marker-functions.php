<?php

/**
 * Return the svg data of the badge icon, used for dynamic coloring
 *
 * @param $baseDir = QA_HTML_THEME_LAYER_URLTOROOT
 * @param $id the id the svg tag should be given
 * @return string
 */
function qa_get_badge_svg($baseDir, $id)
{
    $filePath = $baseDir . 'qa-shield-gen.svg';
    $svgFile = fopen($filePath, "r");
    $text = fread($svgFile,filesize($filePath));
    fclose($svgFile);
    return str_replace('class="svg"', 'class="' . $id . '"', $text);
}