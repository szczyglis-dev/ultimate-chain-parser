<?php

namespace Szczyglis\ChainParser\Core;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class Html
 * @package Szczyglis\ChainParser\Core
 */
class Html
{
    /**
     * @return int
     */
    public function getInputLimit()
    {
        return Config::DEMO_MODE_INPUT_LIMIT;
    }

    /**
     * @return int
     */
    public function getChainLengthLimit()
    {
        return Config::DEMO_MODE_CHAIN_LENGTH_LIMIT;
    }

    /**
     * @return int
     */
    public function getOptionLimit()
    {
        return Config::DEMO_MODE_OPTION_LIMIT;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return Config::VERSION;
    }

    /**
     * @return string
     */
    public function getGitHubUrl()
    {
        return Config::GITHUB_URL;
    }

    /**
     * @return string
     */
    public function getWebpagebUrl()
    {
        return Config::WEB_URL;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCsrfToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = base64_encode(random_bytes(128));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * @param string $token
     * @return bool
     */
    public function isTokenValid(string $token)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        $stored = $_SESSION['csrf_token'];
        if (strcmp($stored, $token) === 0) {
            return true;
        }
    }

    /**
     * @return false|string
     */
    public function restore()
    {
        $request = Request::createFromGlobals();
        $submitted = [];
        $plugins = [];
        $hidden = [];
        $active = [];
        $isAjax = true;
        $isAll = false;
        $input = false;

        if ($request->isMethod('POST') && $request->request->has('form')) {
            $form = $request->request->all('form');
            if (isset($form['is_output_all']) && $form['is_output_all'] == 1) {
                $isAll = true;
            }
            if (isset($form['input'])) {
                $input = $form['input'];
            }
            $isAjax = false;
            if (isset($form['plugin_name']) && !empty($form['plugin_name'])) {
                foreach ($form['plugin_name'] as $i => $name) {
                    $options = [];
                    foreach ($form as $key => $values) {
                        if (!is_array($values)) {
                            continue;
                        }
                        foreach ($values as $j => $value) {
                            if ($j === $i) {
                                if ($key == 'is_active') {
                                    $active[$i] = true;
                                }
                                if ($key == 'is_hidden' && $value == 1) {
                                    $hidden[$i] = true;
                                }
                                $options[$key] = $value;
                            }
                        }
                    }
                    if (!in_array($name, $plugins)) {
                        $plugins[] = $name;
                    }
                    $submitted[$i] = $options;
                }
            }
        }

        $options = [];
        foreach ($submitted as $i => $opts) {
            foreach ($opts as $j => $val) {
                $options[] = [
                    'i' => $i,
                    'k' => $j,
                    'v' => $val,
                ];
            }
        }

        if (empty($plugins)) {
            $plugins = [
                'cleaner',
                'parser',
            ];
            $active = [
                0 => true,
                1 => true,
            ];
        }

        $ary = [
            'plugins' => $plugins,
            'options' => $options,
            'active' => $active,
            'is_ajax' => $isAjax,
            'hidden' => $hidden,
            'is_all' => $isAll,
            'input' => $input,
        ];

        return json_encode(json_encode($ary));
    }

    /**
     * @return string
     */
    public function controls()
    {
        return '
	    <input data-option="plugin_name" type="hidden" name="form[plugin_name][]" value=""/>
	    <input data-option="is_hidden" type="hidden" name="form[is_hidden][]" value="0"/>
	    <div class="row">
		    <div class="col-md-10">
			  <div class="row">
			     <div class="col-auto">
			        <h3>
			          <span class="iteration-color"></span> 
			          #<span title="Execution order (iteration index)" class="index-iteration">1</span> [<span class="plugin-name"></span>]               
			          <button title="Move element in the chain down" class="btn btn-sm btn-secondary btn-element-down">DOWN</button>
			          <button title="Move element in the chain up" class="btn btn-sm btn-secondary btn-element-up">UP</button>
			          <button title="Remove element from chain" class="btn btn-sm btn-danger btn-remove-element text-nowrap">X</button> 
			        </h3>
			      </div>
			      <div class="col mt-1 pt-2">
			        <div class="form-check" title="Enable/disable iteration">
			            <input data-option="is_active" name="form[is_active][]" class="form-check-input" type="checkbox" value="1" checked>
			            <label class="form-check-label">
			              Enabled
			            </label>
			          </div>
			     </div>
			  </div>
			 </div>
			 <div class="col-md-2 text-right">
			 	<button type="button" class="btn btn-secondary btn-element-toggle">HIDE</button>
			 </div>
			</div>';
    }

    /**
     * @param string $plugin
     * @param string $key
     * @return string
     */
    public function option(string $plugin, string $key)
    {
        $options = Config::getOptions();
        if (!isset($options[$plugin][$key])) {
            return '<span class="text-danger">[' . $key . '] not exists.</span>';
        }
        switch ($options[$plugin][$key]['type']) {
            case 't':
                return $this->textarea($key, $options[$plugin][$key]);
                break;
            case 'i':
                return $this->input($key, $options[$plugin][$key]);
                break;
            case 'c':
                return $this->checkbox($key, $options[$plugin][$key]);
                break;
        }
    }

    public function options($plugin, $name)
    {
        $html = '';

        switch ($name) {
            case 'io':
                $html = '
                    <div class="col-12">
                        <div class="mt-3">
                            <b>INPUT / OUTPUT</b>                  
                        </div>    
                        <div class="row">
                            <div class="col">
                                '.$this->option($plugin, 'use_dataset').'
                            </div>
                        </div>            
                        <div class="row">
                            <div class="col-md-6">
                                '.$this->option($plugin, 'sep_input_rowset').'
                                '.$this->option($plugin, 'sep_input_row').'
                                '.$this->option($plugin, 'sep_input_col').'
                            </div>
                            <div class="col-md-6">
                                '.$this->option($plugin, 'sep_output_rowset').'
                                '.$this->option($plugin, 'sep_output_row').'
                                '.$this->option($plugin, 'sep_output_col').'
                            </div>
                        </div>                
                    </div>';
                break;
        }  

        return $html;      
    }

    /**
     * @param string $key
     * @param array $option
     * @return string
     */
    public function textarea(string $key, array $option)
    {
        $label = $key;
        $placeholder = '';
        $help = '';
        $syntax = '';
        $example = '';
        $value = '';

        if (isset($option['value']) && !empty($option['value'])) {
            $value = $option['value'];
        }
        if (isset($option['help']) && !empty($option['help'])) {
            $help = $option['help'];
        }
        if (!empty($option['syntax'])) {
            $syntax = '
	      <span class="help-syntax">
	          <span class="k">Syntax:</span> ' . $option['syntax'] . '
	        </span>';
        }
        if (!empty($option['example'])) {
            $example = '
	      <span class="help-example">
	            <span class="k">Example:</span><br/>
	            ' . $option['example'] . '
	          </span>';
        }

        return '
		  <div class="form-group ot">
		      <label><span class="o">OPTION</span>' . $label . '</label>
		      <textarea data-option="' . $key . '" name="form[' . $key . '][]" class="form-control" rows="4" placeholder="' . $placeholder . '">' . $value . '</textarea>
		      <small class="form-text text-muted">' . $help . '
		      ' . $syntax . '
		      ' . $example . '
		      </small>
		    </div>';
    }

    /**
     * @param string $key
     * @param array $option
     * @return string
     */
    public function input(string $key, array $option)
    {
        $label = $key;
        $value = '';
        $placeholder = '';
        $help = '';
        $syntax = '';
        $example = '';
        $type = 'text';
        $max = '';

        if ($this->isDemoMode()) {
            $max = ' maxlength="50"';
        }
        if (isset($option['value']) && !empty($option['value'])) {
            $value = $option['value'];
        }
        if (isset($option['help']) && !empty($option['help'])) {
            $help = $option['help'];
        }
        if (isset($option['type']) && !empty($option['type'])) {
            $type = $option['type'];
        }
        if (isset($option['syntax']) && !empty($option['syntax'])) {
            $syntax = '
	      <span class="help-syntax">
	          <span class="k">Syntax:</span> ' . $option['syntax'] . '
	        </span>';
        }
        if (isset($option['example']) && !empty($option['example'])) {
            $example = '
	      <span class="help-example">
	            <span class="k">Example:</span><br/>
	            ' . $option['example'] . '
	          </span>';
        }

        return '
		  <div class="form-group ot">
		        <label><span class="o">OPTION</span>' . $label . '</label>
		        <input type="' . $type . '" data-option="' . $key . '" name="form[' . $key . '][]" placeholder="' . $placeholder . '" class="form-control" value="' . $value . '" ' . $max . '/>
		        <small class="form-text text-muted">' . $help . '
		      ' . $syntax . '
		      ' . $example . '
		      </small>
		      </div>';
    }

    /**
     * @return bool
     */
    public function isDemoMode()
    {
        return Config::IS_DEMO_MODE;
    }

    /**
     * @param string $key
     * @param array $option
     * @return string
     */
    public function checkbox(string $key, array $option)
    {
        $label = $key;
        $value = $option['value'];
        $help = $option['help'];
        $syntax = '';
        $example = '';
        $checked = '';

        if (isset($option['checked']) && $option['checked'] == true) {
            $checked = 'checked';
        }
        if (!empty($option['syntax'])) {
            $syntax = '
	      <span class="help-syntax">
	        <span class="k">Syntax:</span> ' . $option['syntax'] . '
	      </span>';
        }
        if (!empty($option['example'])) {
            $example = '
	      <span class="help-example">
	        <span class="k">Example:</span><br/>
	        ' . $option['example'] . '
	      </span>';
        }

        return '
		    <div class="form-check mt-2">
		      <input data-option="' . $key . '" name="form[' . $key . '][]" class="form-check-input" type="checkbox" value="' . $value . '" ' . $checked . '>
		      <label class="form-check-label">
		        ' . $label . '
		      </label>
		      <small class="form-text text-muted">' . $help . '
		      ' . $syntax . '
		      ' . $example . '
		      </small>
		    </div>';
    }
}