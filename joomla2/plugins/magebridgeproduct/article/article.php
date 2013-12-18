<?php
/**
 * MageBridge Product plugin - Article
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2013
 * @license GNU Public License
 * @link http://www.yireo.com
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * MageBridge Product-connector to mail a Joomla! article when a customer buys a product
 *
 * @package MageBridge
 */
class plgMageBridgeProductArticle extends MageBridgePlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

    /*
     * Method to check whether this connector is enabled or not
     *
     * @param null
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /*
     * Method to manipulate the MageBridge Product Relation backend-form
     *
     * @param JForm $form The form to be altered
     * @param JForm $data The associated data for the form
     * @return boolean
     */
    public function onMageBridgeProductPrepareForm($form, $data)
    {
		$form->loadFile(dirname(__FILE__).'/form.xml', false);
    }

    /*
     * Method to execute when the product is bought
     * 
     * @param string $article_id
     * @param JUser $user
     * @param int $status
     * @return bool
     */
    public function onPurchase($article_id = null, $user = null, $status = null)
    {
        // Load the article from the database
        $db = JFactory::getDBO();
        $db->setQuery('SELECT `introtext`,`title` FROM `#__content` WHERE `id` = '.(int)$article_id);
        $article = $db->loadObject();
        if (empty($article)) {
            return false;
        }

        // Parse the text a bit
        $article_text = $article->introtext;
        $article_text = str_replace('{name}', $user->name, $article_text);
        $article_text = str_replace('{email}', $user->email, $article_text);

        // Construct other variables
        $app = JFactory::getApplication();
        $sender = array(
            $app->getCfg('mailfrom'),
            $app->getCfg('fromname'),
        );

        // Send the mail
        jimport('joomla.mail.mail');
        $mail = JFactory::getMailer();
        $mail->setSender($sender);
        $mail->addRecipient($user->email);
        $mail->addCC($app->getCfg('mailfrom'));
        $mail->setBody($article_text);
        $mail->setSubject($article->title);
        $mail->isHTML(true);
        $mail->send();

        return true;
    }
}
