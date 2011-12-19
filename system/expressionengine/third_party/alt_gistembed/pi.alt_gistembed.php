<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ALT Gist Embed Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Adrienne L. Travis
 * @link		
 */

$plugin_info = array(
	'pi_name'		=> 'ALT Gist Embed',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Adrienne L. Travis',
	'pi_author_url'	=> 'http://www.utilitarienne.com/',
	'pi_description'=> 'Embed a GitHub Gist',
	'pi_usage'		=> Alt_gistembed::usage()
);

require_once PATH_THIRD .'alt_gistembed/MarkdownExtended/markdown_extended.php';


class Alt_gistembed {

	public $return_data;
    public $url = 'https://raw.github.com/gist/%s';
    public $params = array();
    
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->EE =& get_instance();
        $this->params['id'] = $this->_param('id', FALSE, FALSE, TRUE);
        $this->params['markdown'] = $this->_param('markdown', TRUE, TRUE);
        $this->params['showtitle'] = $this->_param('showtitle', TRUE, TRUE);
        $this->params['showcite'] = $this->_param('showcite', TRUE, TRUE);
        $this->return_data = $this->gist();
        
        } // end public function __construct()
    
    public function gist() {
        $embedcall = file_get_contents(sprintf($this->url, $this->params['id'])); // Don't be afraid of sprintf!
        $container = "<div class=\"gist-contents\">\n\n%s %s\n</div><!-- end .gist-contents -->";
        $citation = '';
  
        if(FALSE == $this->params['showtitle']) {
            $embedcall = strstr($embedcall,"\n");
            }
        
        if(TRUE == $this->params['markdown']) {
            $embedcall = MarkdownExtended($embedcall);
            }
            
        if(TRUE == $this->params['showcite']) {
            $href = '<a href="https://gist.github.com/'.$this->params['id'].'">see original gist on Github.com</a>';
            $citation = "\n\n<div class=\"gist-info\">$href</div>\n";
            }
        
        $returnstring = sprintf($container,$embedcall,$citation);
        
        return $returnstring;
       
        } // end public function embed()
    
    
    /* Thanks to ObjectiveHTML for this function! see https://gist.github.com/1478635 for details! */
    private function _param($param, $default = FALSE, $boolean = FALSE, $required = FALSE) {
            if($required && !$param) show_error('You must define a "'.$param.'" parameter in the '.__CLASS__.' tag.');

            $param = $this->EE->TMPL->fetch_param($param);

            if(FALSE === $param && FALSE !== $default)
            {
                $param = $default;
            }
            else
            {				
                if($boolean)
                {
                    $param = strtolower($param);
                    $param = ('true' == $param || 'yes' == $param || 'TRUE' == $param || 'YES' == $param) ? TRUE : FALSE;
                }			
            }

            return $param;			
        } // end private function _param($param, $default, $boolean, $required)
    
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage() {
		ob_start();
?>

Usage: {exp:alt_gistembed id="xxxxxx"} where id is the ID # of a gist from GitHub.

Optional Parameters:
showtitle (defaults to true): If set to false, strips the first line of the gist (presumably an H1).
showcite (defaults to true): Shows a "see original gist on GitHub" link.
markdown (defaults to true): Whether to run the returned gist through a Markdown parser.

Note that the Markdown parser used is Markdown Extended Extra, https://github.com/egil/php-markdown-extra-extended .
It is a fork of Markdown Extended by Michel Fortin which adds some extra features; among other things it is pretty close 
(but NOT identical) to GitHub-Flavored Markdown.
<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
        }
    } // end public static function usage() 


/* End of file pi.alt_gistembed.php */
/* Location: /system/expressionengine/third_party/alt_gistembed/pi.alt_gistembed.php */