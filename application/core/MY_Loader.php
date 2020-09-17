<?php

class MY_Loader extends CI_Loader 
{
    
    public function template($content_view, $params = []) 
    {        
        $params['global_head'] = $this->view("global-head", $params, TRUE);
        $params['navigation'] = $this->view("navigation", $params, TRUE);
        $params['sidebar'] = $this->view("sidebar", $params, TRUE);
        $params['content'] = $this->view($content_view, $params, TRUE);
        $params['footer'] = $this->view("footer", $params, TRUE);
        $params['global_footer'] = $this->view("global-footer", $params, TRUE);
        $this->view("container", $params);
    }

}