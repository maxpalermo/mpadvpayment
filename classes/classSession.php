<?php

/**
 * 2017 mpSOFT
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    mpSOFT <info@mpsoft.it>
 *  @copyright 2017 mpSOFT Massimiliano Palermo
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of mpSOFT
 */
class classSession {
    /**
     * 
     * @return classSummary $summary object from Session or null
     */
    public static function getSessionSummary()
    {
        //Get session cart summary
        if (!session_id()) {
            session_start();
        }
        /**
         * @var classSummary $summary;
         */
        $summary = $_SESSION['classSummary'];
        if (empty($summary)) {
            return null;
        }
        
        return $summary;
    }
    
    public static function delSessionSummary()
    {
        //Get session cart summary
        if (!session_id()) {
            session_start();
        }
        
        if(!empty($_SESSION['classSummary'])) {
            unset($_SESSION['classSummary']);
        }
    }
    
    /**
     * Save summary object to current session
     * @param classSummary $summary Summary object
     * @return mixed True if object exists, false otherwise, -1 if object is empty
     */
    public static function  setSessionSummary($summary)
    {
        if(empty($summary)) {
            return -1;
        }
         
        if (!session_id()) {
            session_start();
        }
        //SAVE TO SESSION
        $_SESSION['classSummary'] = $summary;
        
        if (!empty($_SESSION['classSummary'])) {
           return true; 
        } else {
            return false;
        }
    }
}
