<?php


namespace SpiceCRM\includes\SysTrashCan;

use SpiceCRM\includes\TimeDate;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceModules;

class SysTrashCan
{

    /**
     * @param $recordtype
     * @param $recordmodule
     * @param $recordid
     * @param string $recordname
     * @param string $linkname
     * @param string $linkmodule
     * @param string $linkid
     * @param string $recorddata
     * @throws \Exception
     */
    static function addRecord($recordtype, $recordmodule, $recordid, $recordname = '', $linkname = '', $linkmodule = '', $linkid = '', $recorddata = '')
    {
        $timedate = TimeDate::getInstance();
        $current_user = AuthenticationController::getInstance()->getCurrentUser();
        $db = DBManagerFactory::getInstance();
        $now = $timedate->nowDb();
        $db->query("INSERT INTO systrashcan (id, transactionid, date_deleted, user_deleted, recordtype, recordmodule, recordid, recordname, linkname, linkmodule, linkid, recorddata) VALUES('" . create_guid() . "', '" . LoggerManager::getLogger()->getTransactionId() . "', '$now', '$current_user->id','$recordtype', '$recordmodule', '$recordid', '$recordname', '$linkname', '$linkmodule', '$linkid', '".base64_encode($recorddata)."' )");
    }

    /**
     * @return array
     * @throws \Exception
     */
    static function getRecords(){
        $db = DBManagerFactory::getInstance();

        $retArray = [];

        $records = $db->query("SELECT systrashcan.*, users.user_name FROM systrashcan, users WHERE systrashcan.user_deleted = users.id AND recordtype = 'bean' AND recovered = '0' ORDER BY date_deleted DESC");
        while($record = $db->fetchByAssoc($records)){
            $retArray[] = $record;
        }

        return $retArray;
    }

    /**
     * @param $transactionid
     * @param $recordid
     * @return array
     * @throws \Exception
     */
    static function getRelated($transactionid, $recordid): array
    {
        $db = DBManagerFactory::getInstance();

        $retArray = [];
        $records = $db->query("SELECT systrashcan.* FROM systrashcan WHERE recordtype = 'related' AND transactionid='$transactionid' AND recordid='$recordid' AND recovered = '0' ORDER BY date_deleted DESC");
        while($record = $db->fetchByAssoc($records)){
            $retArray[] = $record;
        }
        return $retArray;
    }

    /**
     * mark a bean undeleted
     * re-insert related relations if wanted
     * @param $id
     * @param $related
     * @return bool|string
     * @throws \Exception
     */
    public static function recover($id, $related) {
        $db = DBManagerFactory::getInstance();

        $record = $db->fetchByAssoc($db->query("SELECT systrashcan.* FROM systrashcan WHERE id='$id' AND recovered = '0'"));

        if(!$focus = BeanFactory::getBean($record['recordmodule'])){
            // BWC try using object name ... used to be the string saved in recordmodule
            $bean = array_search($record['recordmodule'], SpiceModules::getInstance()->getBeanList());
            $focus = BeanFactory::getBean($bean);
        }
        if($focus->retrieve($record['recordid'], true, false)){
            $focus->mark_undeleted($focus->id);

            if($related){
                $focus->load_relationships();

                // set as recovered
                $db->query("UPDATE systrashcan SET recovered = 1 WHERE id='$id'");

                $relRecords = SysTrashCan::getRelated($record['transactionid'], $focus->id);
                foreach($relRecords as $relRecord){
                    if($focus->{$relRecord['linkname']}) {
                        $focus->{$relRecord['linkname']}->add($relRecord['linkid']);
                    }
                    // set as recovered
                    $db->query("UPDATE systrashcan SET recovered = 1 WHERE id='".$relRecord['id']."'");
                }
            }

        } else {
            return 'unable to load bean';
        }

        return true;
    }
}
