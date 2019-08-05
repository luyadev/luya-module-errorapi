<?php

namespace luya\errorapi;

use luya\base\CoreModuleInterface;

/**
 * Error API Module.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
final class Module extends \luya\base\Module implements CoreModuleInterface
{
    /**
     * @var array The adapters integrated to send the error informations. For example
     * 
     * ```php
     * 'adapters' => [
     *     [
     *         'class' => 'luya\errorapi\adapters\MailAdapter',
     *         'recipient' => ['errors@example.com'],
     *     ],
     *     [
     *         'class' => 'luya\errorapi\adapters\SlackAdapter',
     *         'token' => 'YOUR_SECRET_SLACK_TOKEN',
     *     ],
     *     [
     *         'class' => 'luya\errorapi\adapters\SentryAdapter',
     *         'token' => 'YOUR_SENTRY_USER_AUTH_TOKEN',
     *         'organisation' => 'organisationslug',
     *         'team' => 'teamslug',
     *     ]
     * ]
     * ```
     * @since 2.0.0
     */
    public $adapters = [];

    /**
     * @var string The link to the "create issue" button.
     * @since 1.0.1
     */
    public $issueCreateRepo = 'https://github.com/luyadev/luya';
    
    /**
     * @inheritdoc
     */
    public $urlRules = [
        ['pattern' => 'errorapi/create', 'route' => 'errorapi/default/create'],
        ['pattern' => 'errorapi/resolve', 'route' => 'errorapi/default/resolve'],
    ];

    /**
     * @inheritdoc
     */
    public static function onLoad()
    {
        self::registerTranslation('errorapi', '@errorapi/messages', [
            'errorapi' => 'errorapi.php',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('errorapi', $message, $params);
    }
}
