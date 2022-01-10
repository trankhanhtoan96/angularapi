<?php


namespace SpiceCRM\modules\Inquiries\api\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\ErrorHandlers\BadRequestException;
use SpiceCRM\includes\ErrorHandlers\NotFoundException;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;
use SpiceCRM\includes\SugarObjects\SpiceConfig;
use SpiceCRM\includes\TimeDate;

class InquiriesController
{

    /**
     * @param $req
     * @param $res
     * @param $args
     */
    public function getCatalogs(Request $req, Response $res, array $args): Response {
        

        if(!SpiceConfig::getInstance()->config['catalogorders']['productgroup_id']) return [];

        $group = BeanFactory::getBean('ProductGroups', SpiceConfig::getInstance()->config['catalogorders']['productgroup_id']);

        $products = [];
        $group->load_relationship('products');
        $relatedProducts = $group->get_linked_beans('products', 'Products', [], 0, 100, 0, "product_status = 'active'");
        foreach ($relatedProducts as $relatedProduct) {
            $products[] = [
                'id' => $relatedProduct->id,
                'name' => html_entity_decode($relatedProduct->name, ENT_QUOTES),
                'external_id' => $relatedProduct->ext_id
            ];
        }
        return $res->withJson($products);
    }

    /**
     * create from avada Form in wordpress
     *
     * @param $req
     * @param $res
     * @param $args
     * @return mixed
     */
    public function createFromAvada(Request $req, Response $res, array $args): Response {

        // get the body and parse the params
        $req->getBody()->rewind();
        $params = $req->getBody()->getContents();

        $queryArray = [];
        parse_str($params,$queryArray);

        if(empty($queryArray['form_id'])){
            throw new BadRequestException('missing data');
        }

        if($queryArray['recaptcha_challenge_field'] == 'explicit' && $queryArray['recaptcha_response_field']){
            throw new BadRequestException('missing data');
        }

        $seed = BeanFactory::getBean($args['module']);
        foreach($seed->field_defs as $fieldname => $fielddata){
            if(isset($queryArray[$fieldname])){
                $seed->{$fieldname} = $queryArray[$fieldname];
            }
        }

        $seed->requested_date = TimeDate::getInstance()->nowDb();
        $seed->inquiry_source = 'web';
        $seed->inquiry_type = 'normal';
        $seed->status = 'new';
        $seed->inquiry_source_url = $queryArray['source_url'];

        $seed->save();

        return $res->withJson(['status' => 'OK']);

    }
}
