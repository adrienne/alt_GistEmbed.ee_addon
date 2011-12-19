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
    public $rawurl = 'https://raw.github.com/gist/%s';
    public $jsonurl = 'https://gist.github.com/%s.json';
    public $params = array();
    
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->EE =& get_instance();
        $config = $this->EE->config;
        
        $this->params['id'] = $this->_param('id', FALSE, FALSE, TRUE);
        $this->params['markdown'] = $this->_param('markdown', FALSE, TRUE);
        $this->params['showtitle'] = $this->_param('showtitle', TRUE, TRUE);
        $this->params['showcite'] = $this->_param('showcite', TRUE, TRUE);

        
        } // end public function __construct()
    

    public function gist() {
        $embedcall = file_get_contents(sprintf($this->rawurl,  $this->params['id'])); // Don't be afraid of sprintf!
        $metadata  = file_get_contents(sprintf($this->jsonurl, $this->params['id'])); 
        $metadata = json_decode($metadata,TRUE);
        
        $giststring = '';
       
        if(TRUE == $this->params['markdown']) {
            $giststring = "<div id=\"gist-".$this->params['id']."\" class=\"gist\">\n<div class=\"gist-file\">\n";
            $giststring .= "<div class=\"gist-data\">\n<div class=\"highlight gist-markdown\">".MarkdownExtended($embedcall)."\n</div>\n</div>";
            }
        else {
            $giststring = $metadata['div'];
            $giststring = substr($giststring,0,strpos($giststring,'<div class="gist-meta">'));
            }


        $citation = '';

        if(TRUE == $this->params['showcite']) {
            $href = '<a href="https://gist.github.com/'.$this->params['id'].'">This Gist</a>';
            $owner = ' is owned by <a href="https://github.com/'.$metadata['owner'].'">'.$metadata['owner'].'</a> and ';
            $citation = "\n\n<div class=\"gist-meta\">$href $owner brought to you by <a href=\"http://github.com\">GitHub.com</a></div>\n";
            }
        
        $returnstring = $giststring.$citation."\n</div>\n</div>";
        
        return $returnstring;
       
        } // end public function gist()
    
    
    public function csslink() {
        $returncss = '<style type="text/css">'."\n";
        $returncss .= '@import url(https://gist.github.com/stylesheets/gist/embed.css);'."\n";
        $returncss .= '.gist .gist-markdown { padding: 0 10px; font-family: sans-serif; font-size: 14px; }'."\n";
        $returncss .= '.gist .gist-markdown code { font-size: 100%; font-family: Consolas, "DejaVu Mono", monospace; }'."\n";
        $returncss .= '.gist .gist-syntax pre, .gist .gist-syntax pre div { font-size: 14px; font-family: Consolas, "DejaVu Mono", monospace; }'."\n";
        $returncss .= '</style>'."\n";
        
        return $returncss;
        } // end public function csslink()
    
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

Main plugin usage: {exp:alt_gistembed:gist id="xxxxxx"} where id is the ID # of a gist from GitHub.

Optional Parameters:
showcite (defaults to true): Shows a "see original gist on GitHub" link.
markdown (defaults to true): Whether to run the returned gist through a Markdown parser.

Note that the Markdown parser used is Markdown Extended Extra, https://github.com/egil/php-markdown-extra-extended .
It is a fork of Markdown Extended by Michel Fortin which adds some extra features. Its output is pretty close
(but NOT identical) to GitHub-Flavored Markdown.

You probably also want to embed the CSS in the head of the document; you can do that with the plugin as well.

CSS usage: {exp:alt_gistembed:csslink}

Optional Parameters:
theme (defaults to github): A few different themes will eventually be available.

<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
        }
    } // end public static function usage() 


/* End of file pi.alt_gistembed.php */
/* Location: /system/expressionengine/third_party/alt_gistembed/pi.alt_gistembed.php */