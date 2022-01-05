<?php
namespace SpiceCRM\includes\Logger;


/**
 * Viewing/Selecting from database based SugarCRM Log
 */

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\ErrorHandlers\ForbiddenException;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\authentication\AuthenticationController;

class LogViewer {

    private $maxLength;

    # Constructor. Reads settings from config file.
    public function __construct() {

        # Accessing the log file is allowed only for admins:
        if ( !AuthenticationController::getInstance()->getCurrentUser()->isAdmin() )
            throw ( new ForbiddenException('Forbidden to view the CRM log. Only for admins.'))->setErrorCode('noCRMlogView');

        $config = SpiceConfig::getInstance();
        $this->maxLength = $config->get( 'logger.view.truncateText', 500 ) * 1;

    }

    public function getEntries($queryParams, $period = null ) {
        $db = DBManagerFactory::getInstance();
        $response = ['entries'=>[]];

        $loglevels = ( isset( $queryParams['loglevels'][0] ) ? json_decode( $queryParams['loglevels'] ) : [] );

        $whereClauseParts = [];
        if ( isset( $queryParams['end'][0] )) $whereClauseParts[] = "l.date_entered <= '{$db->quote($queryParams['end'])}'";
        if ( isset( $queryParams['user_id'][0])) $whereClauseParts[] = "l.created_by = '".$db->quote($queryParams['user_id'])."'";
        if ( count( $loglevels )) $whereClauseParts[] = "',".$db->quote( implode(',', $loglevels )).",' like CONCAT('%,',l.log_level,',%')";
        if ( isset( $queryParams['pid'][0])) $whereClauseParts[] = "l.pid = ".( $queryParams['pid']*1 );
        if ( isset( $queryParams['text'][0])) $whereClauseParts[] = "l.description like '%".$db->quote($queryParams['text'])."%'";
        if ( isset( $queryParams['transaction_id'][0])) $whereClauseParts[] = "l.transaction_id = '".$db->quote($queryParams['transaction_id'])."'";
        $whereClause = count( $whereClauseParts ) ? 'WHERE '.implode( ' AND ', $whereClauseParts ):'';
        if ( isset( $queryParams['limit'][0] )) $limit = ( @$queryParams['limit'] * 1 );
        else $limit = 250;

        $sql = 'SELECT u.id as user_id, l.date_entered, l.id, l.pid, l.log_level, l.transaction_id, SUBSTR( l.description, 1, '.$this->maxLength.' ) AS description, l.created_by, u.user_name, if ( LENGTH( l.description ) <> LENGTH( SUBSTR( l.description, 1, '.$this->maxLength.' )), 1, 0 ) AS descriptionTruncated FROM syslogs l LEFT JOIN users u ON u.id = l.created_by '.$whereClause.' ORDER BY l.date_entered DESC';
        $sqlResult = $db->limitQuery( $sql, 0, $limit );

        while ( $row = $db->fetchByAssoc( $sqlResult )) {
            $row['descriptionTruncated'] = (boolean)$row['descriptionTruncated'];
            $row['pid'] = isset( $row['pid'][0]) ? (int)$row['pid']:null;
            $response['entries'][] = $row;
        }

        $response['totalCount'] = $db->getOne('SELECT COUNT(*) FROM syslogs AS l LEFT JOIN users AS u ON l.created_by = u.id '.$whereClause ) * 1;

        return $response;
    }

    function getFullEntry($entryId ) {
        $db = DBManagerFactory::getInstance();

        $sql = 'SELECT id, pid, log_level, description, created_by, transaction_id FROM syslogs WHERE id = \''.$db->quote( $entryId ).'\'';

        $entry = $db->fetchOne( $sql );
        if ( $entry === false )
            throw ( new NotFoundException( 'Log entry not found.'))->setLookedFor( $entryId );

        return $entry;

    }

}
