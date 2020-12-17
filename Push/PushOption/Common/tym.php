<?php


namespace Yinyi\Push\PushOption\Common;


use Yinyi\Push\Jobs\AppTemplateJob;

trait tym
{
    private $phone;
    private $template;
    private $params;

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

    public function init($phone, $template, $params = [])
    {
        $this->phone = $phone;
        $this->template = $template;
        $this->params = $params;
        return $this;
    }

    public function handle()
    {
        $data = [
            'type' => $this->template['type'],
            'title' => $this->template['title'],
            'content' => $this->replaceContent($this->template, $this->params),
            'url' => $this->template['url'],
            'url_type' => $this->template['url_type']
        ];
        dispatch(new AppTemplateJob($this->phone, $data));
    }
}
