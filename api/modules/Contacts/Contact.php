<?php

namespace SpiceCRM\modules\Contacts;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\SugarObjects\templates\person\Person;
use SpiceCRM\includes\authentication\AuthenticationController;

class Contact extends Person
{

    var $table_name = "contacts";
    var $object_name = "Contact";
    var $module_dir = 'Contacts';

    var $relationship_fields = ['account_id' => 'accounts', 'contacts_users_id' => 'user_sync'];

    /**
     * if a contact is related to a user through portal_user_id, overwrite some user field values with the values from the contact
     * @param false $check_notify
     * @param bool $fts_index_bean
     * @return int|string
     */
    public function save($check_notify = false, $fts_index_bean = true)
    {
        if (!empty($this->portal_user_id)) {
            $user = BeanFactory::getBean('Users', $this->portal_user_id);
            $user->salutation = $this->salutation;
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->address_street = $this->primary_address_street;
            $user->address_street_number = $this->primary_address_street_number;
            $user->address_street_number_suffix = $this->primary_address_street_number_suffix;
            $user->address_city = $this->primary_address_city;
            $user->address_postalcode = $this->primary_address_postalcode;
            $user->address_state = $this->primary_address_state;
            $user->address_country = $this->primary_address_country;
            $user->phone_home = $this->phone_home;
            $user->phone_work = $this->phone_work;
            $user->phone_mobile = $this->phone_mobile;
            $user->phone_other = $this->phone_other;
            $user->phone_fax = $this->phone_fax;
            $user->save();
        }
        return parent::save($check_notify, $fts_index_bean);
    }

    function save_relationship_changes($is_update, $exclude = [])
    {

        //if account_id was replaced unlink the previous account_id.
        //this rel_fields_before_value is populated by sugarbean during the retrieve call.
        if (!empty($this->account_id) and !empty($this->rel_fields_before_value['account_id']) and
            (trim($this->account_id) != trim($this->rel_fields_before_value['account_id']))) {
            //unlink the old record.
            $this->load_relationship('accounts');
            $this->accounts->delete($this->id, $this->rel_fields_before_value['account_id']);
            $this->accounts->add($this->account_id);
        } else if (!empty($this->account_id)){
            $this->load_relationship('accounts');
            $this->accounts->add($this->account_id);
        }
        parent::save_relationship_changes($is_update);
    }
}
