<?php


namespace SpiceCRM\modules\Documents\api\controllers;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;

/**
 * Class DocumentsController
 *
 * @package SpiceCRM\modules\Documents\api\controllers
 */
class DocumentsController
{
    /**
     * createas a new revision from a base64 string
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     * @throws NotFoundException
     */
    public function revisionFromBase64(Request $req, Response $res, array $args): Response
    {
        $document = BeanFactory::getBean('Documents', $args['id']);
        if (!$document) {
            throw new NotFoundException('Document not found');
        }

        $body = $req->getParsedBody();

        $documentRevision = BeanFactory::getBean('DocumentRevisions');
        $documentRevision->id = create_guid();
        $documentRevision->new_with_id = true;

        // generate the attachment
        $attachment = SpiceAttachments::saveAttachmentHashFiles('DocumentRevisions', $documentRevision->id, ['filename' => $body['file_name'], 'file' => $body['file'], 'filemimetype' => $body['file_mime_type']]);

        $documentRevision->file_name = $body['file_name'];
        $documentRevision->file_md5 = $attachment[0]['filemd5'];
        $documentRevision->file_mime_type = $body['file_mime_type'];

        $documentRevision->document_id = $document->id;
        $documentRevision->documentrevisionstatus = $body['documentrevisionstatus'];
        $documentRevision->save();

        $document->file_md5 = $attachment[0]['filemd5'];
        $document->save();
    }
}
