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
        'add_cart' => 'Eccube\Form\Type\AddCartType',

        'entity' => 'Symfony\Bridge\Doctrine\Form\Type\EntityType',

        'birthday' => 'Symfony\Component\Form\Extension\Core\Type\BirthdayType',
        'button' => 'Symfony\Component\Form\Extension\Core\Type\ButtonType',
        'checkbox' => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
        'choice' => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
        'collection' => 'Symfony\Component\Form\Extension\Core\Type\CollectionType',
        'country' => 'Symfony\Component\Form\Extension\Core\Type\CountryType',
        'currency' => 'Symfony\Component\Form\Extension\Core\Type\CurrencyType',
        'datetime' => 'Symfony\Component\Form\Extension\Core\Type\DateTimeType',
        'date' => 'Symfony\Component\Form\Extension\Core\Type\DateType',
        'email' => 'Symfony\Component\Form\Extension\Core\Type\EmailType',
        'file' => 'Symfony\Component\Form\Extension\Core\Type\FileType',
        'hidden' => 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
        'integer' => 'Symfony\Component\Form\Extension\Core\Type\IntegerType',
        'language' => 'Symfony\Component\Form\Extension\Core\Type\LanguageType',
        'locale' => 'Symfony\Component\Form\Extension\Core\Type\LocaleType',
        'money' => 'Symfony\Component\Form\Extension\Core\Type\MoneyType',
        'number' => 'Symfony\Component\Form\Extension\Core\Type\NumberType',
        'password' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
        'percent' => 'Symfony\Component\Form\Extension\Core\Type\PercentType',
        'radio' => 'Symfony\Component\Form\Extension\Core\Type\RadioType',
        'range' => 'Symfony\Component\Form\Extension\Core\Type\RangeType',
        'repeated' => 'Symfony\Component\Form\Extension\Core\Type\RepeatedType',
        'reset' => 'Symfony\Component\Form\Extension\Core\Type\ResetType',
        'search' => 'Symfony\Component\Form\Extension\Core\Type\SearchType',
        'submit' => 'Symfony\Component\Form\Extension\Core\Type\SubmitType',
        'text' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
        'textarea' => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
        'time' => 'Symfony\Component\Form\Extension\Core\Type\TimeType',
        'timezone' => 'Symfony\Component\Form\Extension\Core\Type\TimezoneType',
        'url' => 'Symfony\Component\Form\Extension\Core\Type\UrlType',
    );

    /**
     * $app['form.factory']->createBuilder('order'
     * @var array
     */
    private static $SEQ_FORM_TYPE_CREATE_BUILDER = array(
        [T_VARIABLE, '$app'],
        '[',
        [T_CONSTANT_ENCAPSED_STRING, "'form.factory'"],
        ']',
        [T_OBJECT_OPERATOR],
        [T_STRING, 'createBuilder'],
        '(',
        [T_CONSTANT_ENCAPSED_STRING]
    );

    /**
     * $app['form.factory']->createNamedBuilder('..', 'order'
     * @var array
     */
    private static $SEQ_FORM_TYPE_CREATE_NAMED_BUILDER = array(
        [T_VARIABLE, '$app'],
        '[',
        [T_CONSTANT_ENCAPSED_STRING, "'form.factory'"],
        ']',
        [T_OBJECT_OPERATOR],
        [T_STRING, 'createNamedBuilder'],
        '(',
        [T_CONSTANT_ENCAPSED_STRING],
        ',',
        [T_CONSTANT_ENCAPSED_STRING]
    );

    private static $SEQ_FORM_BUILDER_ADD = [
        [T_OBJECT_OPERATOR],
        [T_STRING, 'add'],
        '(',
        [T_CONSTANT_ENCAPSED_STRING],
        ',',
        [T_CONSTANT_ENCAPSED_STRING]
    ];

    private static $SEC_ENTRY_TYPE = [
        [T_CONSTANT_ENCAPSED_STRING, "'entry_type'"],
        [T_DOUBLE_ARROW],
        [T_CONSTANT_ENCAPSED_STRING]
    ];

    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if ($this->isFormType($tokens)) {
            $this->fixTypeNameInFormType($tokens);
        }
        $this->fixTypeNameForFormFactory($tokens);

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

    /**
     * @param Tokens|$tokens
     */
    protected function fixTypeNameForFormFactory($tokens)
    {
        foreach ([self::$SEQ_FORM_TYPE_CREATE_BUILDER, self::$SEQ_FORM_TYPE_CREATE_NAMED_BUILDER, self::$SEQ_FORM_BUILDER_ADD, self::$SEC_ENTRY_TYPE] as $sequence) {
            $currentIndex = 0;
            $matchedTokens = null;
            do {
                $beforeTokenSize = count($tokens);

                $matchedTokens = $this->fixTypeNameForSequence($tokens, $sequence, $currentIndex);
                if ($matchedTokens) {
                    $indexes = array_keys($matchedTokens);
                    $afterTokenSize = count($tokens);
                    $currentIndex = end($indexes) + $afterTokenSize - $beforeTokenSize;
                }
            } while ($matchedTokens);
        }
    }

    private function fixTypeNameForSequence($tokens, $sequence, $currentIndex)
    {
        $matchedTokens = $tokens->findSequence($sequence, $currentIndex);

        if ($matchedTokens) {
            $typeToken = end($matchedTokens);
            $type = preg_replace('/\'(.*)\'/', '$1', $typeToken->getContent());
            if (isset(self::$TYPE_MAP[$type])) {
                $this->fixTypeName($tokens, $matchedTokens, $type);
                $this->addTypeUse($tokens, $type);
            }
        }
        return $matchedTokens;
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