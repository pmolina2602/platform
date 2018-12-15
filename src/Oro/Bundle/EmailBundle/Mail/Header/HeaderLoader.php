<?php

/**
 * This file is a copy of {@see Zend\Mail\Header\HeaderLoader}
 *
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Oro\Bundle\EmailBundle\Mail\Header;

use \Zend\Mail\Header\HeaderLoader as BaseHeaderLoader;

class HeaderLoader extends BaseHeaderLoader
{
    /**
     * {@inheritdoc}
     */
    protected $plugins = [
        'bcc'                       => 'Oro\Bundle\EmailBundle\Mail\Header\Bcc',
        'cc'                        => 'Oro\Bundle\EmailBundle\Mail\Header\Cc',
        'contenttype'               => 'Oro\Bundle\EmailBundle\Mail\Header\ContentType',
        'content_type'              => 'Oro\Bundle\EmailBundle\Mail\Header\ContentType',
        'content-type'              => 'Oro\Bundle\EmailBundle\Mail\Header\ContentType',
        'contenttransferencoding'   => 'Oro\Bundle\EmailBundle\Mail\Header\ContentTransferEncoding',
        'content_transfer_encoding' => 'Oro\Bundle\EmailBundle\Mail\Header\ContentTransferEncoding',
        'content-transfer-encoding' => 'Oro\Bundle\EmailBundle\Mail\Header\ContentTransferEncoding',
        'date'                      => 'Zend\Mail\Header\Date',
        'from'                      => 'Oro\Bundle\EmailBundle\Mail\Header\From',
        'message-id'                => 'Zend\Mail\Header\MessageId',
        'mimeversion'               => 'Zend\Mail\Header\MimeVersion',
        'mime_version'              => 'Zend\Mail\Header\MimeVersion',
        'mime-version'              => 'Zend\Mail\Header\MimeVersion',
        'received'                  => 'Zend\Mail\Header\Received',
        'replyto'                   => 'Zend\Mail\Header\ReplyTo',
        'reply_to'                  => 'Zend\Mail\Header\ReplyTo',
        'reply-to'                  => 'Zend\Mail\Header\ReplyTo',
        'sender'                    => 'Oro\Bundle\EmailBundle\Mail\Header\Sender',
        'subject'                   => 'Oro\Bundle\EmailBundle\Mail\Header\Subject',
        'to'                        => 'Zend\Mail\Header\To',
    ];
}