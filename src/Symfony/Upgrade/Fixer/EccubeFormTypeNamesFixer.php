<?php


namespace Symfony\Upgrade\Fixer;


use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class EccubeFormTypeNamesFixer extends FormTypeNamesFixer
{

    private static $TYPE_MAP = array(
        'name' => 'Eccube\Form\Type\NameType',
        'kana' => 'Eccube\Form\Type\KanaType',
        'tel' => 'Eccube\Form\Type\TelType',
        'fax' => 'Eccube\Form\Type\FaxType',
        'zip' => 'Eccube\Form\Type\ZipType',
        'address' => 'Eccube\Form\Type\AddressType',
        'repeated_email' => 'Eccube\Form\Type\RepeatedEmailType',
        'repeated_password' => 'Eccube\Form\Type\RepeatedPasswordType',
        'price' => 'Eccube\Form\Type\PriceType',
        'master' => 'Eccube\Form\Type\MasterType',
        'job' => 'Eccube\Form\Type\Master\JobType',
        'customer_status' => 'Eccube\Form\Type\Master\CustomerStatusType',
        'order_status' => 'Eccube\Form\Type\Master\OrderStatusType',
        'calc_rule' => 'Eccube\Form\Type\Master\CalcRuleType',
        'sex' => 'Eccube\Form\Type\Master\SexType',
        'disp' => 'Eccube\Form\Type\Master\DispType',
        'pref' => 'Eccube\Form\Type\Master\PrefType',
        'product_type' => 'Eccube\Form\Type\Master\ProductTypeType',
        'product_list_max' => 'Eccube\Form\Type\Master\ProductListMaxType',
        'product_list_order_by' => 'Eccube\Form\Type\Master\ProductListOrderByType',
        'page_max' => 'Eccube\Form\Type\Master\PageMaxType',
        'csv_type' => 'Eccube\Form\Type\Master\CsvType',
        'delivery_date' => 'Eccube\Form\Type\Master\DeliveryDateType',
        'payment' => 'Eccube\Form\Type\Master\PaymentType',
        'mail_template' => 'Eccube\Form\Type\Master\MailTemplateType',
        'category' => 'Eccube\Form\Type\Master\CategoryType',
        'tag' => 'Eccube\Form\Type\Master\TagType',
        'customer' => 'Eccube\Form\Type\CustomerType',
        'search_product' => 'Eccube\Form\Type\SearchProductType',
        'search_product_block' => 'Eccube\Form\Type\SearchProductBlockType',
        'order_search' => 'Eccube\Form\Type\OrderSearchType',
        'shipping_item' => 'Eccube\Form\Type\ShippingItemType',
        'shipping_multiple' => 'Eccube\Form\Type\ShippingMultipleType',
        'shipping_multiple_item' => 'Eccube\Form\Type\ShippingMultipleItemType',
        'shopping' => 'Eccube\Form\Type\ShoppingType',
        'entry' => 'Eccube\Form\Type\Front\EntryType',
        'contact' => 'Eccube\Form\Type\Front\ContactType',
        'nonmember' => 'Eccube\Form\Type\Front\NonMemberType',
        'shopping_shipping' => 'Eccube\Form\Type\Front\ShoppingShippingType',
        'customer_address' => 'Eccube\Form\Type\Front\CustomerAddressType',
        'forgot' => 'Eccube\Form\Type\Front\ForgotType',
        'customer_login' => 'Eccube\Form\Type\Front\CustomerLoginType',
        'admin_login' => 'Eccube\Form\Type\Admin\LoginType',
        'admin_change_password' => 'Eccube\Form\Type\Admin\ChangePasswordType',
        'admin_product' => 'Eccube\Form\Type\Admin\ProductType',
        'admin_product_class' => 'Eccube\Form\Type\Admin\ProductClassType',
        'admin_search_product' => 'Eccube\Form\Type\Admin\SearchProductType',
        'admin_search_customer' => 'Eccube\Form\Type\Admin\SearchCustomerType',
        'admin_search_order' => 'Eccube\Form\Type\Admin\SearchOrderType',
        'admin_customer' => 'Eccube\Form\Type\Admin\CustomerType',
        'admin_class_name' => 'Eccube\Form\Type\Admin\ClassNameType',
        'admin_class_category' => 'Eccube\Form\Type\Admin\ClassCategoryType',
        'admin_category' => 'Eccube\Form\Type\Admin\CategoryType',
        'admin_member' => 'Eccube\Form\Type\Admin\MemberType',
        'admin_authority_role' => 'Eccube\Form\Type\Admin\AuthorityRoleType',
        'admin_page_layout' => 'Eccube\Form\Type\Admin\PageLayoutType',
        'admin_news' => 'Eccube\Form\Type\Admin\NewsType',
        'admin_template' => 'Eccube\Form\Type\Admin\TemplateType',
        'admin_security' => 'Eccube\Form\Type\Admin\SecurityType',
        'admin_csv_import' => 'Eccube\Form\Type\Admin\CsvImportType',
        'shop_master' => 'Eccube\Form\Type\Admin\ShopMasterType',
        'tradelaw' => 'Eccube\Form\Type\Admin\TradelawType',
        'order' => 'Eccube\Form\Type\Admin\OrderType',
        'order_detail' => 'Eccube\Form\Type\Admin\OrderDetailType',
        'shipping' => 'Eccube\Form\Type\Admin\ShippingType',
        'shipment_item' => 'Eccube\Form\Type\Admin\ShipmentItemType',
        'payment_register' => 'Eccube\Form\Type\Admin\PaymentRegisterType',
        'tax_rule' => 'Eccube\Form\Type\Admin\TaxRuleType',
        'main_edit' => 'Eccube\Form\Type\Admin\MainEditType',
        'mail' => 'Eccube\Form\Type\Admin\MailType',
        'customer_agreement' => 'Eccube\Form\Type\Admin\CustomerAgreementType',
        'block' => 'Eccube\Form\Type\Admin\BlockType',
        'delivery' => 'Eccube\Form\Type\Admin\DeliveryType',
        'delivery_fee' => 'Eccube\Form\Type\Admin\DeliveryFeeType',
        'delivery_time' => 'Eccube\Form\Type\Admin\DeliveryTimeType',
        'admin_system_log' => 'Eccube\Form\Type\Admin\LogType',
        'admin_cache' => 'Eccube\Form\Type\Admin\CacheType',
        'admin_system_masterdata' => 'Eccube\Form\Type\Admin\MasterdataType',
        'admin_system_masterdata_data' => 'Eccube\Form\Type\Admin\MasterdataDataType',
        'admin_system_masterdata_edit' => 'Eccube\Form\Type\Admin\MasterdataEditType',
        'plugin_local_install' => 'Eccube\Form\Type\Admin\PluginLocalInstallType',
        'plugin_management' => 'Eccube\Form\Type\Admin\PluginManagementType',
    );

    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            $this->fixTypeNameInFormType($tokens);
        } else {
            $this->fixTypeNameForFormFactory($tokens);
        }

        return $tokens->generateCode();
    }

    protected function fixTypeNameInFormType($tokens)
    {
        foreach (array_keys(self::$TYPE_MAP) as $type) {
            if (null === $this->matchTypeName($tokens, $type)) {
                continue;
            }

            $this->addTypeUse($tokens, $type);
            $this->fixTypeNames($tokens, $type);
        }
    }

    protected function fixTypeNameForFormFactory($tokens)
    {
        $currentIndex = 0;
        $matchedTokens = null;
        do {
            $beforeTokenSize = count($tokens);
            $matchedTokens = $tokens->findSequence([
                [T_VARIABLE, '$app'],
                '[',
                [T_CONSTANT_ENCAPSED_STRING, "'form.factory'"],
                ']',
                [T_OBJECT_OPERATOR],
                [T_STRING, 'createBuilder'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING]
            ], $currentIndex);
            if ($matchedTokens) {
                $typeToken = end($matchedTokens);
                $type = preg_replace('/\'(.*)\'/', '$1', $typeToken->getContent());
                if (isset(self::$TYPE_MAP[$type])) {
                    $this->fixTypeName($tokens, $matchedTokens, $type);
                    $this->addTypeUse($tokens, $type);
                }
            }
            $afterTokenSize = count($tokens);
            $currentIndex += $afterTokenSize - $beforeTokenSize;
        } while ($matchedTokens);
    }

    protected function addTypeUse(Tokens $tokens, $name)
    {
        $this->addUseStatement(
            $tokens,
            $this->getFormTypeFQCN($name)
        );
    }

    protected function fixTypeNames(Tokens $tokens, $name)
    {
        $matchedTokens = $this->matchTypeName($tokens, $name);
        if (null === $matchedTokens) {
            return;
        }

        $this->fixTypeName($tokens, $matchedTokens, $name);

        $this->fixTypeNames($tokens, $name);
    }

    protected function fixTypeName($tokens, $matchedTokens, $name)
    {
        $matchedIndexes = array_keys($matchedTokens);

        $matchedIndex = $matchedIndexes[count($matchedIndexes) - 1];

        $fqcn = $this->getFormTypeFQCN($name);
        $tokens->insertAt(
            $matchedIndex,
            [
                new Token([T_STRING, end($fqcn)]),
                new Token([T_DOUBLE_COLON, '::']),
            ]
        );
        $matchedTokens[$matchedIndex]->override([CT_CLASS_CONSTANT, 'class']);
    }

    private function getFormTypeFQCN($name)
    {
        return explode('\\', self::$TYPE_MAP[$name]);
    }

    public function getDescription()
    {
        return "EC-CUBE FormType support.";
    }

}