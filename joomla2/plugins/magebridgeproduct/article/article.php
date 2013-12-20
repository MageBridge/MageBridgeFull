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
 * MageBridge Product Plugin to mail a Joomla! article when a customer buys a product
 *
 * @package MageBridge
 */
class plgMageBridgeProductArticle extends MageBridgePluginProduct
{
    /*
     * Deprecated variable to migrate from the original connector-architecture to new Product Plugins
     */
    protected $connector_field = 'article_id';

    /*
     * Method to execute when the product is bought
     * 
     * @param array $actions
     * @param JUser $user
     * @param int $status
     * @return bool
     */
    public function onMageBridgeProductPurchase($actions = null, $user = null, $status = null)
    {
        // Check for the article ID
        if(!isset($actions['article_id'])) {
            return false;
        }

        // Make sure it is not empty
        $article_id = (int)$actions['article_id'];
        if(!$article_id > 0) {
            return false;
        }

        $this->db->setQuery('SELECT `introtext`,`title` FROM `#__content` WHERE `id` = '.$article_id);
        $article = $this->db->loadObject();
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
