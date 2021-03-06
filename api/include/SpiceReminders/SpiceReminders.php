<?php

namespace SpiceCRM\includes\SpiceReminders;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;
use SugarThemeRegistry;

class SpiceReminders
{

    public static function setReminderRaw($beanId, $beanModule, $reminderDate)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();
        $dbReminderDate = $reminderDate;
        if (DBManagerFactory::getInstance()->dbType == 'mysql')
            $db->query("INSERT INTO spicereminders SET user_id = '$current_user->id', bean='$beanModule', bean_id = '$beanId', reminder_date='$dbReminderDate' ON DUPLICATE KEY UPDATE reminder_date='$dbReminderDate'");
        else {
            $reminderRecordObj = $db->query("SELECT * FROM spicereminders WHERE user_id='$current_user->id' AND bean_id='$beanId'");
            if ($reminderRecord = $db->fetchByAssoc($reminderRecordObj))
                $db->query("UPDATE spicereminders SET reminder_date='$dbReminderDate'WHERE user_id='$current_user->id' AND bean_id='$beanId'");
            else
                $db->query("INSERT INTO spicereminders (user_id, bean, bean_id, reminder_date) VALUES ('$current_user->id','$beanModule', '$beanId', '$dbReminderDate' )");
        }
        //$thisDate = $reminderDate;
        return true;
    }

    public static function getReminder($beanId)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $reminderObj = $db->query("SELECT * FROM spicereminders WHERE user_id='$current_user->id' AND bean_id='$beanId'");
        //if($db->getRowCount($reminderObj) > 0)
        if ($reminderRow = $db->fetchByAssoc($reminderObj)) {
            // $reminderRow = $db->fetchByAssoc($reminderObj);
            if (DBManagerFactory::getInstance()->dbType == 'mssql')
                $reminderRow['reminder_date'] = str_replace('.000', '', $reminderRow['reminder_date']);
            return $GLOBALS['timedate']->to_display_date($reminderRow['reminder_date'], false); //. SugarThemeRegistry::current()->getImage('close_inline');
        } else
            return '';
    }

    public static function removeReminder($beanId)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $db->query("DELETE FROM spicereminders WHERE user_id='$current_user->id' AND bean_id='$beanId'");
    }

    public static function loadReminders(){
        return SpiceReminders::getRemindersRaw(50);
    }

    public static function getRemindersRaw($lastN = 10)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $favArray = [];

        if (!empty($lastN)) {
            $lastNObj = $db->limitQuery("SELECT * FROM spicereminders WHERE user_id='$current_user->id' ORDER BY reminder_date ASC", 0, $lastN);
        } else
            $lastNObj = $db->query("SELECT * FROM spicereminders WHERE user_id='$current_user->id' ORDER BY reminder_date ASC");


        while ($lastNRow = $db->fetchByAssoc($lastNObj)) {
            if (DBManagerFactory::getInstance()->dbType == 'mssql')
                $lastNRow['reminder_date'] = str_replace('.000', '', $lastNRow['reminder_date']);

            $thisBean = BeanFactory::getBean($lastNRow['bean'], $lastNRow['bean_id']);
            $summary = $thisBean ? $thisBean->get_summary_text() : '';
            $favArray[] = [
                'item_id' => $lastNRow['bean_id'],
                'module_name' => $lastNRow['bean'],
                'item_summary' => $summary,
                'item_summary_short' => substr($summary, 0, 15),
                'reminder_date' => $lastNRow['reminder_date']
            ];
            $thisBean = null;
            unset($thisBean);
        }
        return $favArray;
    }

    public static function getReminders($lastN = 10)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $favArray = [];
        $lastNObj = $db->limitQuery("SELECT * FROM spicereminders WHERE user_id='$current_user->id' ORDER BY reminder_date ASC ", 0, $lastN);
        while ($lastNRow = $db->fetchByAssoc($lastNObj)) {
            if (DBManagerFactory::getInstance()->dbType == 'mssql')
                $lastNRow['reminder_date'] = str_replace('.000', '', $lastNRow['reminder_date']);

            $thisBean = BeanFactory::getBean($lastNRow['bean'], $lastNRow['bean_id']);
            if($thisBean) {
                $summaryText = $thisBean ? $thisBean->get_summary_text() : '';
                $favArray[] = [
                    'bean_id' => $lastNRow['bean_id'],
                    'bean' => $lastNRow['bean'],
                    'summary' => (strlen($summaryText) > 15 ? substr($summaryText, 0, 13) . '...' : $summaryText),
                    'reminder_date' => $GLOBALS['timedate']->to_display_date($lastNRow['reminder_date'], false),
                    'icon' => SugarThemeRegistry::current()->getImage($lastNRow['bean'])
                ];
            }
            $thisBean = null;
            unset($thisBean);
        }
        return $favArray;
    }

    public static function getReminderCount($lastN = 10)
    {
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
$db = DBManagerFactory::getInstance();
        $count = 0;
        $lastNObj = $db->limitQuery("SELECT * FROM spicereminders WHERE user_id='$current_user->id' ORDER BY reminder_date ASC ", 0, $lastN);
        while ($lastNRow = $db->fetchByAssoc($lastNObj)) {
            $count++;
        }
        return $count;
    }
}
