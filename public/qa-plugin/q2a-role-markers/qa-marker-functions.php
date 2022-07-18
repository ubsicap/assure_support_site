<?php

/**
 * Return the svg data of the badge icon, used for dynamic coloring
 *
 * @param $id the id the svg tag should be given
 * @return string
 */
function qa_get_badge_svg($id)
{
    $filePath = './qa-plugin/q2a-role-markers/qa-shield-gen1.svg';
    $svgFile = fopen($filePath, "r");
    $text = fread($svgFile,filesize($filePath));
    fclose($svgFile);
    return str_replace('class="svg"', 'class="' . $id . '"', $text);
}