<?php


namespace Yinyi\Push\PushOption\Common;


trait tym
{
    /**
     * 替换推送内容
     *
     * @param $content
     *
     * @return string
     */
    public function replaceContent($template, $params)
    {
        $keywords = explode(',', $template['keywords']);
        $content = $template['content'];
        foreach ($keywords as $keyword){
            $content = str_replace('{$'. $keyword. '}', $params[$keyword], $content);
        }
        return $content;
    }
}
