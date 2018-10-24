<?php
/**
 * CSRF Protection Class
 */
class CSRF_Protection
{
  /**
   * Holds CI instance
   *
   * @var CI instance
   */
  private $CI;
 
  /**
   * Name used to store token on session
   *
   * @var string
   */
  private static $token_name = 'csrf_akel_name';
 
  /**
   * Stores the token
   *
   * @var string
   */
  private static $token;
 
  // -----------------------------------------------------------------------------------
 
  public function __construct()
  {
    $this->CI =& get_instance();
  }
  
  /**
	 * Generates a CSRF token and stores it on session. Only one token per session is generated.
	 * This must be tied to a post-controller hook, and before the hook
	 * that calls the inject_tokens method().
	 *
	 * @return void
	 * @author Muhammad Tanzeel
  */
	public function generate_token()
	{
	  // Load session library if not loaded
	  $this->CI->load->library('session');
	  $this->CI->load->library('security');
	 
	  if ($this->CI->session->userdata($this->CI->security->get_csrf_token_name()) === FALSE)
	  {
	 
	    $this->CI->session->set_userdata($this->CI->security->get_csrf_token_name(), $this->CI->security->get_csrf_hash());
	  }
	}
	
	/**
	 * Validates a submitted token when POST request is made.
	 *
	 * @return void
	 * @author Ian Murray
	 */
	public function validate_tokens()
	{
	  
	  $this->CI->load->library('security');
	  // Is this a post request?
	  if ($_SERVER['REQUEST_METHOD'] == 'POST')
	  {
	    // Is the token field set and valid?
	    $posted_token = $this->CI->input->post($this->CI->security->get_csrf_token_name());
	    if ($posted_token === FALSE || $posted_token != $this->CI->session->userdata($this->CI->security->get_csrf_token_name()))
	    {
	      // Invalid request, send error 400.
	      show_error('Request was invalid. Tokens did not match.', 400);
	    }
	  }
	}
	
	/**
	 * This injects hidden tags on all POST forms with the csrf token.
	 * Also injects meta headers in <head> of output (if exists) for easy access
	 * from JS frameworks.
	 *
	 * @return void
	 * @author Ian Murray
	 */
	public function inject_tokens()
	{
	  
	  $this->CI->load->library('security');
	  $output = $this->CI->output->get_output();
	 
	  // Inject into form
	  $output = preg_replace('/(<(form|FORM)[^>]*(method|METHOD)="(post|POST)"[^>]*>)/',
	                         '$0<input type="hidden" name="'.$this->CI->security->get_csrf_token_name().'" value="'.$this->CI->security->get_csrf_hash().'">', 
	                         $output);
	 
	  // Inject into <head>
	  $output = preg_replace('/(<\/head>)/',
	                         '<meta name="csrf-name" content="'.$this->CI->security->get_csrf_token_name().'">' . "\n" . '<meta name="csrf-token" content="'.$this->CI->security->get_csrf_hash().'">' . "\n" . '$0', 
	                         $output);
	 
	  $this->CI->output->_display($output);
	}
  
}