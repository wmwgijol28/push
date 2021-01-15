<?php


namespace Yinyi\Push\PushOption\Common;


use Yinyi\Push\Jobs\AppTemplateJob;

trait tym
{
    private $phone;
    private $template;
    private $params;
    private $urlParams;

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


    public function init($phone, $template, $params = [], $urlParams = [])
    {
        $this->phone = $phone;
        $this->template = $template;
        $this->params = $params;
        $this->urlParams = $urlParams;
        return $this;
    }

    public function handle()
    {
        $data = [
            'type' => $this->template['type'],
            'title' => $this->template['title'],
            'content' => $this->replaceContent($this->template, $this->params),
            'url' => $this->setUrl(),
            'url_type' => $this->template['url_type']
        ];
        dispatch(new AppTemplateJob($this->phone, $data));
    }


    private function setUrl()
    {
        if(in_array($this->template['url_type'], [0, 2]) || !$this->urlParams){
            return $this->template['url'];
        }
        return $this->template['url']. '?'. join('&', configArray($this->urlParams));
    }
}
