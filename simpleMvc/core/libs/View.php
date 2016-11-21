<?php
/**
 * SimpleMvc
 *
 * @copyright 2016-2017 Jamie Kim
 */
namespace SimpleMvc;

/**
 * View Class
 */
class View 
{
    protected $settings;
    
    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function rendering($file, $viewData, $header=null, $footer = null)
    {
        $eval_header = null;
        $eval_footer = null;
        
        if($this->settings->debug_mode) {
            echo '<pre>' . var_export($viewData, true) . '</pre>';
        }

        //defaul settings to show html
        $this->setDefault_eval($viewData);

        //set data.
        $eval_forms = $this->file_get_contents_with_eval($file, $viewData);

        //set header and footer if it is needed
        if(!empty($header) && !empty($footer)) {
            
            //get header and footer
            $eval_header = $this->file_get_contents_with_eval($header, $viewData);
            $eval_footer = $this->file_get_contents_with_eval($footer, $viewData);

            //get whole page view
            $eval_forms = $eval_header . $eval_forms . $eval_footer;
        }
        
        echo $eval_forms;
    }
    
    private function file_get_contents_with_eval($file, $viewData)
    {
        $search = array();
        $replace = array();
        
        foreach ($viewData as $key => $value) {
            array_push($search, '{' . $key . '}');
            array_push($replace, $value);
        }

        $forms = file_get_contents($file);
        $eval_contents = str_replace($search, $replace, $forms);

        return $eval_contents;
    }
    
    private function setDefault_eval(&$viewData)
    {
        $defaultData = [
            //base url
            $this->settings->base_url_view_name => rtrim($this->settings->base_url,"/"),
            //app title
            $this->settings->app_title_view_name => rtrim($this->settings->app_title),
            //view url
            $this->settings->style_url_view_name => $this->getUrlPath(VIEWS)
        ];
        
        //combine it to view data
        $viewData = array_merge($defaultData, $viewData);
    }
    
    private function getUrlPath($path)
    {
        $docRoot = dirname($_SERVER['SCRIPT_FILENAME']);
        $relPath = str_replace($docRoot, '', $path);
        $urlPath = $this->settings->base_url . $relPath . DS . $this->settings->theme;
        
        return $urlPath;
    }
}